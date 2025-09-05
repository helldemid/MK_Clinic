<?php
namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Appointment;
use App\Entity\AppointmentGuestContact;
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
		$html = $this->renderView('components/request_details.html.twig', [
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
				// Заполняем поля из запроса
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
			$em->persist($appointment);
			$em->flush();


			return $this->renderStatus('Success', 'New appointment created successfully!<br><a href="' . $this->generateUrl('appointment_new') . '">Create another appointment</a>', 1);
		}

		return $this->render('appointment/new.html.twig', [
			'form' => $form->createView(),
		]);
	}
}
