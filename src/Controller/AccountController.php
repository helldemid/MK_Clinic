<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Appointment;
use App\Entity\AppointmentRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\LoginFormAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\EmailVerificationService;
use App\Service\MailerService;
use App\Form\ChangePasswordType;
use App\Form\ChangeEmailType;
use App\Form\ForgotPasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Dto\FullnameInput;
use App\Dto\PhoneInput;
use App\Dto\ChangeEmailDto;
use App\Controller\Traits\FormErrorTrait;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use Symfony\Component\Form\FormError;
use App\Controller\Traits\StatusRenderTrait;

use App\Repository\UserRepository;

class AccountController extends AbstractController
{

	use FormErrorTrait;
	use StatusRenderTrait;
	public const TYPE_FORGOT_PASSWORD = 1;
	public const TYPE_CREATE_ACCOUNT = 2;
	public const TYPE_CHANGE_EMAIL = 3;
	public const TYPE_CHANGE_PASSWORD = 4;

	public const CONFIRMATION_ACTIONS_NAMES = [
		self::TYPE_FORGOT_PASSWORD => 'app_forgot_password',
		self::TYPE_CREATE_ACCOUNT => 'app_create_account',
		self::TYPE_CHANGE_EMAIL => 'app_change_email',
		self::TYPE_CHANGE_PASSWORD => 'app_change_password'
	];

	public const CONFIRMATION_EMAIL_TEMPLATES = [
		self::TYPE_FORGOT_PASSWORD => MailerService::TEMPLATE_FORGOT_PASSWORD,
		self::TYPE_CREATE_ACCOUNT => MailerService::TEMPLATE_EMAIL_VERIFICATION,
		self::TYPE_CHANGE_EMAIL => MailerService::TEMPLATE_CHANGE_EMAIL,
	];

	private EmailVerificationService $emailVerificationService;
	private MailerService $mailerService;
	private EntityManagerInterface $entityManager;
	private UserAuthenticatorInterface $userAuthenticator;
	private Security $security;
	private LoginFormAuthenticator $authenticator;
	private UserPasswordHasherInterface $passwordHasher;

	private UserRepository $userRepository;

	public function __construct(
		EmailVerificationService $emailVerificationService,
		MailerService $mailerService,
		EntityManagerInterface $entityManager,
		UserAuthenticatorInterface $userAuthenticator,
		LoginFormAuthenticator $authenticator,
		Security $security,
		UserPasswordHasherInterface $passwordHasher,
		UserRepository $userRepository
	) {
		$this->emailVerificationService = $emailVerificationService;
		$this->mailerService = $mailerService;
		$this->entityManager = $entityManager;
		$this->userAuthenticator = $userAuthenticator;
		$this->security = $security;
		$this->authenticator = $authenticator;
		$this->passwordHasher = $passwordHasher;
		$this->userRepository = $userRepository;
	}


	/**
	 * User registration
	 *
	 * @param Request $request
	 */
	#[Route('/account', name: 'app_account')]
	public function account(Request $request)
	{
		$user = $this->getUser();

		if (null === $user) {
			return $this->redirectToRoute('app_login');
		}

		if ($this->isGranted('ROLE_ADMIN')) {
			$users = $this->userRepository->createQueryBuilder('u')
				->where('u.id != :currentId')
				->setParameter('currentId', $user->getId())
				->orderBy('u.id', 'DESC')
				->getQuery()
				->getResult();

			$appointmentRequests = $this->entityManager->getRepository(AppointmentRequest::class)
				->createQueryBuilder('ar')
				->orderBy('ar.createdAt', 'DESC')
				->getQuery()
				->getResult();

			$appointments = $this->entityManager->getRepository(Appointment::class)
				->createQueryBuilder('a')
				->orderBy('a.appointmentDate', 'DESC')
				->getQuery()
				->getResult();
		}

		return $this->render('account/index.html.twig', [
			'user' => $user, 'users' => $users ?? [],
			'appointmentRequests' => $appointmentRequests ?? [],
			'appointments' => $appointments ?? []
		]);
	}

	#[Route('/account/user/{id}/change-role', name: 'admin_change_role', methods: ['POST'])]
	/**
	 * Change the role of a user.
	 *
	 * This endpoint allows a SUPER_ADMIN to change a user's role.
	 *
	 * @param Request $request
	 * @param User $user The user entity resolved by ParamConverter
	 * @param EntityManagerInterface $em
	 * @return JsonResponse
	 */
	public function changeRole(Request $request, User $user, EntityManagerInterface $em): JsonResponse
	{
		try {
			// Deny access if current user is not a SUPER_ADMIN
			$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

			// Decode JSON payload
			$data = json_decode($request->getContent(), true);
			$role = $data['role'] ?? null;

			// Validate role
			$allowedRoles = ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN'];
			if (!$role || !in_array($role, $allowedRoles, true)) {
				return $this->json([
					'success' => false,
					'error' => 'Invalid role'
				], 400);
			}

			// Prevent changing own role
			if ($this->getUser()->getId() === $user->getId()) {
				return $this->json([
					'success' => false,
					'error' => 'You cannot change your own role'
				], 403);
			}

			// Update user's role
			$user->setRoles([$role]);
			$em->flush();

			return $this->json(['success' => true, 'role' => $role]);
		} catch (\Throwable $e) {
			// Catch any unexpected errors
			return $this->json([
				'success' => false,
				'message' => 'An unexpected error occurred',
				'error' => $e->getMessage() // optional for debug
			], 500);
		}
	}


	#[Route('/account/user/{id}/toggle-active', name: 'admin_toggle_active', methods: ['POST'])]
	/**
	 * Toggle the "active" status of a user.
	 *
	 * This endpoint allows a SUPER_ADMIN to activate or deactivate other users.
	 *
	 * @param Request $request
	 * @param User $user The user entity resolved by ParamConverter
	 * @param EntityManagerInterface $em
	 * @return JsonResponse
	 */
	public function toggleActive(
		Request $request,
		User $user,
		EntityManagerInterface $em
	): JsonResponse {
		try {
			// Deny access if the current user is not a SUPER_ADMIN
			$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

			// Decode JSON payload
			$data = json_decode($request->getContent(), true);

			// Validate incoming data
			if (!isset($data['isActive'])) {
				return $this->json(['success' => false, 'message' => 'Missing isActive field'], 400);
			}

			$isActive = filter_var($data['isActive'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
			if ($isActive === null) {
				return $this->json(['success' => false, 'message' => 'Invalid isActive value'], 400);
			}

			// Prevent user from toggling their own status
			if ($this->getUser()->getId() === $user->getId()) {
				return $this->json(['success' => false, 'message' => 'You cannot change your own status'], 403);
			}

			// Update the user's active status
			$user->setIsActive($isActive);
			$em->flush();

			// Return the updated status
			return $this->json(['success' => true, 'isActive' => $user->isActive()]);
		} catch (\Throwable $e) {
			// Catch any exception and return a safe JSON response
			return $this->json([
				'success' => false,
				'message' => 'An unexpected error occurred',
				'error' => $e->getMessage() // optional: remove in production
			], 500);
		}
	}


	/**
	 * Create a confirmation link that will be send for action verification
	 * @param string $code
	 * @param string $action
	 * @return string
	 */
	private function createConfirmationLink(string $code, string $action): string
	{
		return $this->generateUrl('app_email_verified', [
			'action' => $action,
			'code' => $code,
		], Response::HTTP_SEE_OTHER);
	}

	/**
	 * Send confirmation email to user
	 *
	 * @param string $userEmail
	 * @param integer $user_id
	 * @param integer $confirmType
	 * @param string $userName
	 *
	 * @return void
	 */
	private function sendConfirmationLetter(string $userEmail, int $user_id, int $confirmType, string $userName = 'dear fellow user'): void
	{
		$confirmationCode = $this->emailVerificationService->create($userEmail, $user_id, $confirmType);

		$confirmationLink = $this->createConfirmationLink($confirmationCode, self::CONFIRMATION_ACTIONS_NAMES[$confirmType]);

		$this->mailerService->sendTemplateEmail(
			$userEmail,
			self::CONFIRMATION_EMAIL_TEMPLATES[$confirmType],
			[
				'name' => $userName,
				'verification_link' => $confirmationLink,
			]
		);
	}

	/**
	 * Process email verification actions
	 *
	 * @param Request $request
	 * @return Response
	 */
	#[Route('/email_verified', name: 'app_email_verified')]
	public function emailVerified(Request $request): Response
	{
		$confirmationCode = $request->get('code');
		$action = $request->get('action');

		try {
			if (!$confirmationCode || !$action)
				throw new \Exception('Verification link is invalid');

			$verificationData = $this->emailVerificationService->confirm($confirmationCode);

			if (empty($verificationData) || empty($verificationData['action']))
				throw new \Exception('Verification code is invalid or has expired');

			if (!array_key_exists($verificationData['action'], self::CONFIRMATION_ACTIONS_NAMES)) {
				throw new \Exception('Unknown action for verification code');
			}

			$actionToCall = self::CONFIRMATION_ACTIONS_NAMES[$verificationData['action']] . '_confirmed';

			if (!is_callable([$this, $actionToCall])) {
				throw new \Exception('Action method not found: ' . $actionToCall);
			}

		} catch (\Exception $e) {
			return $this->renderStatus('Confirmation Error', $e->getMessage(), 0);
		}

		return $this->$actionToCall($verificationData['user_id']);
	}

	/**
	 * User registration
	 *
	 * @param Request $request
	 */
	#[Route('/account/create_account', name: 'app_create_account')]
	public function createAccount(Request $request)
	{
		$userEmail = $request->get('email');
		$userId = $request->get('user_id');
		$sUserName = $request->get('name');

		if (!$userEmail || !$userId) {
			return $this->renderStatus('Confirmation Error', 'Some error occurred while creating your account. Please try again later.', 0);
		}

		$this->sendConfirmationLetter($userEmail, $userId, self::TYPE_CREATE_ACCOUNT, $sUserName);

		return $this->renderStatus('Please confirm your email address', 'Check your inbox – we have sent you an email!');
	}

	/**
	 * Forgot password handler
	 *
	 * @param Request $request
	 * @return Response
	 */
	#[Route('/account/forgot_password', name: 'app_forgot_password')]
	public function forgotPassword(Request $request): Response
	{
		$form = $this->createForm(ForgotPasswordType::class);
		$form->handleRequest($request);

		$error = null;

		if ($form->isSubmitted() && $form->isValid()) {
			$userEmail = $form->get('email')->getData();
			$user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

			if (!$user) {
				$error = 'User not found';
			} else {
				$this->sendConfirmationLetter($userEmail, $user->getId(), self::TYPE_FORGOT_PASSWORD, $user->getFirstName());
				return $this->renderStatus('Email Sent', 'If you do not receive the email within a few minutes, please check your spam filter settings and try again.');
			}
		}

		return $this->render('account/forgot_password.html.twig', [
			'form' => $form->createView(),
			'error' => $error,
		]);
	}

	/**
	 * Change password handler
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/account/change_password', name: 'app_change_password')]
	public function changePassword(Request $request)
	{
		$token = $request->get('token');
		$bForgetPass = null !== $token;

		if ($this->getUser()) {
			$user = $this->getUser();
		} elseif ($bForgetPass) {
			// password reset from email verification link
			$verificationData = $this->emailVerificationService->confirm($token);
			if (!$verificationData || empty($verificationData['user_id'])) {
				return $this->renderStatus('Password Reset Error', 'Invalid or expired token', 0);
			}
			$user = $this->entityManager->getRepository(User::class)->find($verificationData['user_id']);
			if (!$user) {
				return $this->renderStatus('Password Reset Error', 'User not found', 0);
			}
		} else {
			// no permission
			return $this->redirectToRoute('app_login');
		}

		$form = $this->createForm(ChangePasswordType::class);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$newPassword = $form->get('password')->getData();
			$user->setPassword($this->passwordHasher->hashPassword($user, $newPassword));
			$this->entityManager->flush();

			if ($token) {
				$this->emailVerificationService->remove($user->getEmail());
			}

			return $this->renderStatus('Password Changed', 'Your password has been successfully changed!');
		}

		return $this->render('account/change_password.html.twig', [
			'form' => $form->createView(),
			'bForgetPass' => $bForgetPass,
			'userName' => $user->getFirstName()
		]);
	}

	/**
	 * Change email handler
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @return Response|\Symfony\Component\HttpFoundation\RedirectResponse
	 */
	#[Route('/account/change_email', name: 'app_change_email')]
	public function changeEmail(Request $request)
	{
		$token = $request->get('token');

		// verified email change from email verification link
		if (null !== $token) {
			// email change from email verification link
			$verificationData = $this->emailVerificationService->confirm($token);
			if (!$verificationData || empty($verificationData['user_id'])) {
				return $this->renderStatus('Email Change Error', 'Invalid or expired token', 0);
			}
			$user = $this->entityManager->getRepository(User::class)->find($verificationData['user_id']);
			if (!$user) {
				return $this->renderStatus('Email Change Error', 'User not found', 0);
			}
			$user->setEmail($verificationData['email']);
			$this->entityManager->flush();
			$this->emailVerificationService->remove($user->getEmail());
			return $this->renderStatus('Email Changed', 'Your email address has been successfully changed!');
		}

		$currentUser = $this->getUser();
		if (!$currentUser) {
			return $this->redirectToRoute('app_login');
		}

		$dto = new ChangeEmailDto();
		$form = $this->createForm(ChangeEmailType::class, $dto);
		$form->handleRequest($request);

		if ($request->isMethod('POST') && $form->isSubmitted()) {
			$newEmail = $dto->email;
			$confirmEmail = $dto->confirmEmail;

			if ($newEmail !== $confirmEmail) {
				$form->get('confirmEmail')->addError(new FormError('Emails do not match.'));
			}
			$exists = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $newEmail]);
			if ($exists) {
				$form->get('email')->addError(new FormError('Email is already in use.'));
			}

			if ($form->isValid()) {
				$this->sendConfirmationLetter($newEmail, $currentUser->getId(), self::TYPE_CHANGE_EMAIL, $currentUser->getFirstName());
				return $this->renderStatus(
					'Please confirm your new email address',
					'Check your inbox – we have sent you an email!'
				);
			}
		}

		return $this->render('account/change_email.html.twig', [
			'form' => $form->createView()
		]);
	}

	/**
	 * Edit fullname handler (AJAX)
	 *
	 * @param Request $request
	 * @param EntityManagerInterface $em
	 * @param ValidatorInterface $validator
	 * @return JsonResponse
	 */
	#[Route('/account/edit_fullname', name: 'app_edit_fullname', methods: ['POST'])]
	public function editFullname(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
	{
		if (!$this->getUser()) {
			throw $this->createAccessDeniedException();
		}

		$input = new FullnameInput();
		$input->firstName = trim((string) $request->request->get('firstName', ''));
		$input->lastName = trim((string) $request->request->get('lastName', ''));

		$violations = $validator->validate($input);

		if (count($violations) > 0) {
			return $this->json([
				'success' => false,
				'errors' => $this->violationsToArray($violations),
			], 422);
		}

		$user = $this->getUser();
		$user->setFirstName($input->firstName);
		$user->setLastName($input->lastName);
		$em->flush();

		return $this->json([
			'success' => true,
			'firstName' => $user->getFirstName(),
			'lastName' => $user->getLastName(),
		]);
	}

	/**
	 * Edit phone handler (AJAX)
	 *
	 * @param \Symfony\Component\HttpFoundation\Request $request
	 * @param \Doctrine\ORM\EntityManagerInterface $em
	 * @param \Symfony\Component\Validator\Validator\ValidatorInterface $validator
	 * @return JsonResponse
	 */
	#[Route('/account/edit_phone', name: 'app_edit_phone', methods: ['POST'])]
	public function editPhone(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
	{
		if (!$this->getUser()) {
			throw $this->createAccessDeniedException();
		}

		$input = new PhoneInput();
		// Можно нормализовать пробелы: +44 7123 456 789 → +44 7123 456 789 (оставим как есть для читаемости)
		$input->phone = preg_replace('/\s+/', ' ', trim((string) $request->request->get('phone', '')));

		$violations = $validator->validate($input);

		if (count($violations) > 0) {
			return $this->json([
				'success' => false,
				'errors' => $this->violationsToArray($violations),
			], 422);
		}

		$user = $this->getUser();
		$existingUser = $em->getRepository(User::class)->findOneBy(['phone' => $input->phone]);

		if ($existingUser && $existingUser->getId() !== $user->getId()) {
			return $this->json([
				'success' => false,
				'errors' => ['phone' => 'This phone number is already in use.'],
			], 422);
		}

		$user->setPhone($input->phone);
		$em->flush();

		return $this->json([
			'success' => true,
			'phone' => $user->getPhone(),
		]);
	}

	/**
	 * Activates user after email confirmation
	 *
	 * @param integer $userId
	 * @return Response
	 */
	private function app_create_account_confirmed(int $userId): Response
	{
		// get user by ID
		$user = $this->entityManager->getRepository(User::class)->find($userId);

		if (!$user) {
			return $this->renderStatus('Confirmation failed', 'User not found', 0);
		}

		// activate user
		$user->setIsActive(true);
		$this->entityManager->flush();

		$this->emailVerificationService->remove($user->getEmail());

		// notification about successful confirmation
		return $this->renderStatus('Account Verified', 'Your account has been activated');
	}

	/**
	 * Allow password reset from email verification link
	 *
	 * @param integer $userId
	 * @return void
	 */
	private function app_forgot_password_confirmed(int $userId)
	{
		$user = $this->entityManager->getRepository(User::class)->find($userId);

		if (!$user) {
			return $this->renderStatus('Confirmation failed', 'User not found', 0);
		}

		$token = $this->emailVerificationService->getToken($user->getId(), self::TYPE_FORGOT_PASSWORD);

		return $this->redirectToRoute('app_change_password', ['token' => $token]);
	}

	/**
	 * Allow email change from email verification link
	 *
	 * @param integer $userId
	 * @return void
	 */
	private function app_change_email_confirmed(int $userId)
	{
		$user = $this->entityManager->getRepository(User::class)->find($userId);

		if (!$user) {
			return $this->renderStatus('Confirmation failed', 'User not found', 0);
		}

		$token = $this->emailVerificationService->getToken($user->getId(), self::TYPE_CHANGE_EMAIL);

		return $this->redirectToRoute('app_change_email', ['token' => $token]);
	}

	#[Route('/account/appointments/history', name: 'account_appointments_history')]
	public function history(Request $request, EntityManagerInterface $em): JsonResponse
	{
		$user = $this->getUser();
		$offset = max(0, (int)$request->query->get('offset', 0));
		$limit = min(20, (int)$request->query->get('limit', 10));

		$appointments = $em->getRepository(Appointment::class)
			->createQueryBuilder('a')
			->where('a.user = :user')
			->setParameter('user', $user)
			->orderBy('a.appointmentDate', 'DESC')
			->setFirstResult($offset)
			->setMaxResults($limit + 1)
			->getQuery()
			->getResult();

		$hasMore = count($appointments) > $limit;
		if ($hasMore) {
			array_pop($appointments);
		}

		$items = [];
		foreach ($appointments as $appointment) {
			$items[] = [
				'html' => $this->renderView('components/timeline_item.html.twig', [
					'date' => $appointment->getAppointmentDate()->format('d/m/Y H:i'),
					'status' => $appointment->getStatus(),
					'treatment' => $appointment->getTreatment()->getName(),
					'therapist' => $appointment->getDoctor() ? $appointment->getDoctor() : 'No therapist assigned',
					'status_label' => match ($appointment->getStatus()) {
						'scheduled' => 'Scheduled',
						'no_show' => 'No show',
						'completed' => 'Completed',
						'cancelled' => 'Cancelled',
						default => 'Unknown',
					}
				])
			];
		}

		return $this->json(['items' => $items, 'hasMore' => $hasMore]);
	}

}