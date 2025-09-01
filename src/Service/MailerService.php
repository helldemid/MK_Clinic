<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MailerService
{
	private const FROM_EMAIL = 'noreply@mkaestheticclinic.com ';

	// constants of template emails
	public const TEMPLATE_EMAIL_VERIFICATION = [
		'subject' => "Confirm your email on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/email_verification.html.twig',
	];
	public const TEMPLATE_FORGOT_PASSWORD = [
		'subject' => "Reset your password on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/forgot_password.html.twig',
	];
	public const TEMPLATE_CHANGE_EMAIL = [
		'subject' => "Change your email on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/change_email.html.twig',
	];

	public const TEMPLATE_APPOINTMENT_REQUEST_TO_US = [
		'subject' => "New appointment request on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/appointment_request_to_us.html.twig',
	];

	public const TEMPLATE_APPOINTMENT_REQUEST_TO_CLIENT = [
		'subject' => "You have requested an appointment on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/appointment_request_to_client.html.twig',
	];

	public const TEMPLATE_CONSULTATION_REQUEST_TO_US = [
		'subject' => "New consultation request on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/consultation_request_to_us.html.twig',
	];

	public const TEMPLATE_CONSULTATION_REQUEST_TO_CLIENT = [
		'subject' => "You have requested a consultation on M.K. Aesthetic Clinic",
		'twig_path' => 'emails/consultation_request_to_client.html.twig',
	];



	private MailerInterface $mailer;
	private Environment $twig;

	public function __construct(MailerInterface $mailer, Environment $twig)
	{
		$this->mailer = $mailer;
		$this->twig = $twig;
	}

	/**
	 * Summary of sendEmail
	 * @param string $to
	 * @param string $subject
	 * @param string $htmlContent
	 * @return void
	 */
	public function sendEmail(string $to, string $subject, string $htmlContent): void
	{
		$email = (new Email())
			->from(self::FROM_EMAIL)
			->to($to)
			->subject($subject)
			->html($htmlContent);

		$this->mailer->send($email);
	}

	/**
	 * Summary of sendTemplateEmail
	 * @param string $to
	 * @param array $template
	 * @param array $context
	 * @return void
	 */
	public function sendTemplateEmail(string $to, array $template, array $context = []): void
	{
		$htmlContent = $this->twig->render($template['twig_path'], $context);

		$email = (new Email())
			->from(self::FROM_EMAIL)
			->to($to)
			->subject($template['subject'])
			->html($htmlContent);

		$this->mailer->send($email);
	}
}
