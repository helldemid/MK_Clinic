<?php
namespace App\Controller;

use App\Entity\AppointmentRequest;
use App\Entity\Treatments;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use App\Service\MailerService;

use App\Repository\TreatmentsRepository;

class AppointmentRequestController extends AbstractController
{

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
		return $this->render('messages/confirm_success.html.twig', [
			'title' => 'Success',
			'message' => 'Your appointment request has been successfully submitted. We will get back to you shortly.',
		]);
	}

	#[Route('/consultation/success', name: 'consultation_success', methods: ['GET'])]
	public function successConsultation()
	{
		return $this->render('messages/confirm_success.html.twig', [
			'title' => 'Success',
			'message' => 'Your consultation request has been successfully submitted. We will get back to you shortly.',
		]);
	}

	#[Route('/book_now', name: 'book_now')]
	public function successBookNow(TreatmentsRepository $tRepo)
	{
		$treatments = $tRepo->findAll();
		return $this->render('book/book.html.twig', [
			'treatments' => $treatments
		]);
	}
}
