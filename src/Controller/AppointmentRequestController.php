<?php
namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Appointment;
use App\Entity\AppointmentGuestContact;
use App\Entity\AppointmentPayment;
use App\Entity\Treatments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Form\AppointmentType;
use App\Service\MailerService;

use App\Repository\TreatmentsRepository;

use App\Controller\Traits\StatusRenderTrait;


class AppointmentRequestController extends AbstractController
{

	use StatusRenderTrait;

	private const CLINIC_EMAIL = 'mkaestheticclinics@gmail.com';
	private const TEST_EMAIL = 'hellboy08090@gmail.com';

	private MailerService $mailerService;

	public function __construct(MailerService $mailerService)
	{
		$this->mailerService = $mailerService;
	}

	#[Route('/appointment/request', name: 'appointment_request', methods: ['POST'])]
	public function appointmentRequest(
		Request $request,
		EntityManagerInterface $em,
		ValidatorInterface $validator
	): JsonResponse {
		$data = (0 === strpos($request->headers->get('Content-Type'), 'application/json'))
			? json_decode($request->getContent(), true)
			: $request->request->all();

		$appointment = new AppointmentRequest();
		$appointment->setName($data['name'] ?? '');
		$appointment->setPhone($data['phone'] ?? '');
		$appointment->setEmail($data['email'] ?? null);
		$appointment->setQuestion($data['question'] ?? null);

		if ($this->getUser()) {
			$appointment->setUser($this->getUser());
		}

		// If treatment is specified
		if (!empty($data['treatment_id'])) {
			$treatment = $em->getRepository(Treatments::class)->find($data['treatment_id']);
			if (!$treatment) {
				return $this->json(['success' => false, 'message' => 'Invalid treatment selected', 'errors' => ['treatment' => 'Invalid treatment selected']], 400);
			}
			$appointment->setTreatment($treatment);
		}

		// Validate the entity
		$errors = $validator->validate($appointment);
		if (count($errors) > 0) {
			$err = [];
			foreach ($errors as $e) {
				$err[$e->getPropertyPath()] = $e->getMessage();
			}
			return $this->json(['success' => false, 'errors' => $err, 'message' => 'Invalid form data'], 400);
		}

		$em->persist($appointment);
		$em->flush();

		// Send email to clinic and to client
		$this->mailerService->sendTemplateEmail(self::TEST_EMAIL, MailerService::TEMPLATE_APPOINTMENT_REQUEST_TO_US, [
			'appointment' => $appointment
		]);
		$this->mailerService->sendTemplateEmail($appointment->getEmail(), MailerService::TEMPLATE_APPOINTMENT_REQUEST_TO_CLIENT, [
			'appointment' => $appointment
		]);

		return $this->json(['success' => true]);
	}

	#[Route('/consultation/request', name: 'consultation_request', methods: ['POST'])]
	public function consultationRequest(
		Request $request,
		EntityManagerInterface $em,
		ValidatorInterface $validator
	): JsonResponse {
		$data = (0 === strpos($request->headers->get('Content-Type'), 'application/json'))
			? json_decode($request->getContent(), true)
			: $request->request->all();
		$currentUser = $this->getUser();

		$appointment = new AppointmentRequest();
		$appointment->setName($data['name'] ?? '');
		$appointment->setPhone($data['phone'] ?? '');
		$appointment->setEmail($currentUser ? $currentUser->getEmail() : null);
		$appointment->setQuestion($data['question'] ?? null);
		$appointment->setTreatment(null);

		if ($currentUser) {
			$appointment->setUser($currentUser);
		}

		// Validate the entity
		$errors = $validator->validate($appointment);
		if (count($errors) > 0) {
			$err = [];
			foreach ($errors as $e) {
				$err[$e->getPropertyPath()] = $e->getMessage();
			}
			return $this->json(['success' => false, 'errors' => $err, 'message' => 'Invalid form data'], 400);
		}

		$em->persist($appointment);
		$em->flush();

		// Send email to clinic and to client
		$this->mailerService->sendTemplateEmail(self::TEST_EMAIL, MailerService::TEMPLATE_CONSULTATION_REQUEST_TO_US, [
			'appointment' => $appointment
		]);
		if ($currentUser) {
			$this->mailerService->sendTemplateEmail($currentUser->getEmail(), MailerService::TEMPLATE_CONSULTATION_REQUEST_TO_CLIENT, [
				'appointment' => $appointment
			]);
		}

		return $this->json(['success' => true]);
	}

	#[Route('/appointment/success', name: 'appointment_success', methods: ['GET'])]
	public function successAppointment()
	{
		return $this->renderStatus('Success', 'Your appointment request has been successfully submitted. We will get back to you shortly.', 1);
	}

	#[Route('/consultation/success', name: 'consultation_success', methods: ['GET'])]
	public function successConsultation()
	{
		return $this->renderStatus('Success', 'Your consultation request has been successfully submitted. We will get back to you shortly.', 1);
	}

	#[Route('/book_now', name: 'book_now')]
	public function successBookNow(TreatmentsRepository $tRepo)
	{
		$treatments = $tRepo->findAll();
		return $this->render('book/book.html.twig', [
			'treatments' => $treatments
		]);
	}

	#[Route('/appointment/request/{id}/change-status', name: 'request_change_status', methods: ['POST'])]
	/**
	 * Change the status of an appointment request.
	 *
	 * This endpoint allows a SUPER_ADMIN to change the status of an appointment request.
	 *
	 * @param Request $request
	 * @param AppointmentRequest $appointmentRequest The appointment request entity resolved by ParamConverter
	 * @param EntityManagerInterface $em
	 * @return JsonResponse
	 */
	public function changeStatus(Request $request, AppointmentRequest $appointmentRequest, EntityManagerInterface $em): JsonResponse
	{
		try {
			// Deny access if current user is not a SUPER_ADMIN
			$this->denyAccessUnlessGranted('ROLE_ADMIN');

			// Decode JSON payload
			$data = json_decode($request->getContent(), true);
			$status = $data['status'] ?? null;

			// Update appointment request status
			$appointmentRequest->setStatus($status);
			$em->flush();

			return $this->json(['success' => true, 'status' => $status]);
		} catch (\Throwable $e) {
			// Catch any unexpected errors
			return $this->json([
				'success' => false,
				'message' => 'An unexpected error occurred',
				'error' => $e->getMessage() // optional for debug
			], 500);
		}
	}

	#[Route('/admin/request/{id}/details', name: 'admin_request_details', methods: ['GET'])]
	public function requestDetails(AppointmentRequest $request): JsonResponse
	{
		// Deny access if current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$html = $this->renderView('modals/request_details.html.twig', [
			'request' => $request,
		]);
		return $this->json([
			'success' => true,
			'html' => $html,
		]);
	}

	#[Route('/appointment/new', name: 'appointment_new')]
	public function new(Request $request, EntityManagerInterface $em): Response
	{
		// Deny access if current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$appointment = new Appointment();

		// Check if we come from appointment request
		$requestId = $request->query->get('request_id');
		if ($requestId) {
			$appointmentRequest = $em->getRepository(AppointmentRequest::class)->find($requestId);
			if ($appointmentRequest) {
				// Fill fields from request
				if ($appointmentRequest->getUser()) {
					$appointment->setUser($appointmentRequest->getUser());
				} else {
					$guestContact = new AppointmentGuestContact();
					$guestContact->setName($appointmentRequest->getName());
					$guestContact->setPhone($appointmentRequest->getPhone());
					$guestContact->setEmail($appointmentRequest->getEmail());
					$appointment->setGuestContact($guestContact);
				}
				if ($appointmentRequest->getTreatment()) {
					$appointment->setTreatment($appointmentRequest->getTreatment());
				}
			}
		}

		$form = $this->createForm(AppointmentType::class, $appointment, [
			'patientType' => $appointment->getGuestContact() ? 'guest' : 'user',
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$appointment->setCreatedBy($this->getUser());

			// Create payment entity and link it to appointment
			$appointmentPayment = new AppointmentPayment();
			$appointmentPayment->setAmount($appointment->getTreatment() ? $appointment->getTreatment()->getPrice()->getPrice() : 0.0);

			$appointment->setPayment($appointmentPayment);

			$em->persist($appointment);
			$em->flush();

			$patientEmail = $appointment->getUser()
				? $appointment->getUser()->getEmail()
				: ($appointment->getGuestContact() ? $appointment->getGuestContact()->getEmail() : null);

			if ($patientEmail) {
				// Send email to client
				$this->mailerService->sendTemplateEmail($patientEmail, MailerService::NEW_APPOINTMENT, [
					'appointment' => [
						'fullName' => $appointment->getUser() ? $appointment->getUser()->getFullName() : $appointment->getGuestContact()->getName(),
						'treatmentName' => $appointment->getTreatment() ? $appointment->getTreatment()->getName() : 'N/A',
						'appointmentDate' => $appointment->getAppointmentDate(),
						'doctor' => $appointment->getDoctor() ? $appointment->getDoctor() : 'Specialist not assigned',
					]
				]);
			}

			return $this->renderStatus('Success', 'New appointment created successfully!<br><a href="' . $this->generateUrl('appointment_new') . '">Create another appointment</a>', 1);
		}

		return $this->render('appointment/new.html.twig', [
			'form' => $form->createView(),
		]);
	}

	#[Route('/appointment/{id}/edit-appointment', name: 'appointment_edit', methods: ['POST'])]
	public function edit(Request $request, EntityManagerInterface $em, Appointment $appointment): JsonResponse
	{
		// Deny access if current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$requestData = json_decode($request->getContent(), true);
		$field = $requestData['field'] ?? null;
		$value = $requestData['value'] ?? null;

		try {
			if ($field && $value) {
				$setter = 'set' . ucfirst($field);
				if (method_exists($appointment, $setter)) {
					$appointment->$setter($value);
					$em->flush();
				} else {
					throw new \InvalidArgumentException('Invalid field: ' . $field);
				}
			} else {
				throw new \InvalidArgumentException('Field and value must be provided');
			}
		} catch (\Throwable $e) {
			return $this->json(['success' => false, 'error' => $e->getMessage(), 'message' => 'Updating failed. Try again later.'], 500);
		}

		return $this->json(['success' => true], 200);
	}

	#[Route('/appointment/{id}/send-change-notification', name: 'appointment_send_notification', methods: ['POST'])]
	public function sendAppointmentChangesNotification(Request $request, Appointment $appointment): JsonResponse
	{
		// Deny access if current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$patientEmail = $appointment->getEmail();

		if (!$patientEmail) {
			return $this->json([
				'success' => false,
				'error' => 'No email found for this appointment. Cannot send notification.',
				'message' => 'Notification sending failed. Try again later.'
			], 400);
		}

		$requestData = json_decode($request->getContent(), true);

		$allowedFields = ['status', 'appointmentDate', 'doctor', 'treatment'];
		$templateData = [];

		foreach ($allowedFields as $field) {
			$templateData[$field] = array_key_exists($field, $requestData) ? $requestData[$field] : false;
		}

		if (empty($templateData)) {
			return $this->json([
				'success' => false,
				'message' => 'No valid fields to notify about.',
				'error' => 'No valid fields in request.'
			], 400);
		}

		$this->mailerService->sendTemplateEmail($patientEmail, MailerService::APPOINTMENT_CHANGED, $templateData);

		return $this->json(['success' => true], 200);
	}

	#[Route('/admin/appointment/{id}/details', name: 'admin_appointment_details', methods: ['GET'])]
	public function appointmentDetails(Appointment $appointment, EntityManagerInterface $em): JsonResponse
	{
		// Deny access if current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_ADMIN');

		$bAppointmentEditable = $appointment->getStatus() !== 'completed';
		$treatments = $em->getRepository(Treatments::class)->findAll();
		$html = $this->renderView('modals/appointment_details.html.twig', [
			'appointment' => $appointment,
			'bNotifyToCall' => $appointment->getStatus() === 'scheduled' && $appointment->getAppointmentDate() <= new \DateTime('+1 day'),
			'bOverdue' => $appointment->getStatus() === 'scheduled' && $appointment->getAppointmentDate() < new \DateTime('now'),
			'bUserDataEditable' => !$appointment->getUser() && $bAppointmentEditable,
			'bAppointmentEditable' => $bAppointmentEditable,
			'treatments' => $treatments,
			'paymentMethods' => AppointmentPayment::PAYMENT_METHODS,
			'paymentStatuses' => AppointmentPayment::PAYMENT_STATUSES,
			'appointmentStatuses' => Appointment::APPOINTMENT_STATUSES,
		]);
		return $this->json([
			'success' => true,
			'html' => $html,
		]);
	}

	/**
	 * Update appointment details (status, date, doctor, treatment, payment, contacts).
	 *
	 * Returns JSON with:
	 *  - success: true|false
	 *  - errors: array of validation errors (if any)
	 *  - changedImportantFields: array of fields that were updated and may require user notification
	 */
	#[Route('/admin/appointment/{id}/update', name: 'admin_appointment_update', methods: ['POST'])]
	public function update(Request $request, Appointment $appointment, EntityManagerInterface $em): JsonResponse
	{
		$data = json_decode($request->getContent(), true);
		$errors = [];
		$changedImportantFields = [];

		// Store old values for comparison
		$oldStatus = $appointment->getStatus();
		$oldDate = $appointment->getAppointmentDate() ? clone $appointment->getAppointmentDate() : null;
		$oldDoctor = $appointment->getDoctor();
		$oldTreatment = $appointment->getTreatment();

		// --- Status ---
		if (isset($data['status'])) {
			$validStatuses = array_keys(Appointment::APPOINTMENT_STATUSES);
			if (!in_array($data['status'], $validStatuses, true)) {
				$errors['status'] = 'Invalid status value';
			} else {
				$appointment->setStatus($data['status']);
				if ($data['status'] !== $oldStatus) {
					$changedImportantFields['status'] = $data['status'];
				}
			}
		}

		// --- Appointment date ---
		if (isset($data['appointmentDate'])) {
			try {
				$newDate = new \DateTime($data['appointmentDate']);
				$appointment->setAppointmentDate($newDate);
				if (!$oldDate || $newDate != $oldDate) {
					$changedImportantFields['appointmentDate'] = $newDate->format('Y-m-d H:i:s');
				}
			} catch (\Exception $e) {
				$errors['appointmentDate'] = 'Invalid date format';
			}
		}

		// --- Doctor ---
		if (isset($data['doctor'])) {
			$doctor = trim((string) $data['doctor']);
			$appointment->setDoctor($doctor);
			if ($oldDoctor !== $doctor) {
				$changedImportantFields['doctor'] = $doctor;
			}
		}

		// --- Treatment ---
		if (isset($data['treatment'])) {
			$treatment = $data['treatment']
				? $em->getRepository(Treatments::class)->find($data['treatment'])
				: null;
			if (!$treatment && $data['treatment']) {
				$errors['treatment'] = 'Treatment not found';
			} else {
				$appointment->setTreatment($treatment);
				$oldTreatmentId = $oldTreatment?->getId();
				$newTreatmentId = $treatment?->getId();

				if ($oldTreatmentId !== $newTreatmentId) {
					$changedImportantFields['treatment'] = $treatment?->getName();
				}

			}
		}

		// --- Payment ---
		if ($appointment->getPayment()) {
			if (isset($data['payment_status'])) {
				$appointment->getPayment()->setStatus($data['payment_status']);
			}
			if (isset($data['payment_method'])) {
				$appointment->getPayment()->setMethod($data['payment_method']);
			}
			if (isset($data['payment_amount'])) {
				if (!is_numeric($data['payment_amount']) || $data['payment_amount'] < 0) {
					$errors['payment_amount'] = 'Invalid payment amount';
				} else {
					$appointment->getPayment()->setAmount((float) $data['payment_amount']);
				}
			}
		}

		// --- Guest contact data ---
		if ($appointment->getGuestContact()) {
			if (isset($data['patient_name'])) {
				if (trim($data['patient_name']) === '') {
					$errors['patient_name'] = 'Name cannot be empty';
				} else {
					$appointment->getGuestContact()->setName($data['patient_name']);
				}
			}

			if (isset($data['patient_phone'])) {
				$phone = preg_replace('/\s+/', '', $data['patient_phone']); // remove spaces
				$ukPhonePattern = '/^(?:\+44\d{10}|0\d{10})$/';
				if (!preg_match($ukPhonePattern, $phone)) {
					$errors['patient_phone'] = 'Invalid UK phone number';
				} else {
					$appointment->getGuestContact()->setPhone($phone);
				}
			}

			if (isset($data['patient_email'])) {
				if (!filter_var($data['patient_email'], FILTER_VALIDATE_EMAIL)) {
					$errors['patient_email'] = 'Invalid email format';
				} else {
					$appointment->getGuestContact()->setEmail($data['patient_email']);
				}
			}
		}


		// Save only if no validation errors
		if (empty($errors)) {
			$em->flush();
		}

		return $this->json([
			'success' => empty($errors),
			'errors' => $errors,
			'changedImportantFields' => $changedImportantFields,
		]);
	}

}
