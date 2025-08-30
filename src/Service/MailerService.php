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


	private MailerInterface $mailer;
	private Environment $twig;

	public function __construct(MailerInterface $mailer, Environment $twig)
	{
		$this->mailer = $mailer;
		$this->twig = $twig;
	}

	/**
	 * Отправка простого письма
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
