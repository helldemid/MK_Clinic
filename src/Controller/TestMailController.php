<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class TestMailController extends AbstractController
{
	#[Route('/test-mail', name: 'test_mail')]
	public function index(MailerInterface $mailer): Response
	{
		$email = (new Email())
			->from('noreply@mkaestheticclinic.com')
			->to('d.demydovych1@gmail.com')
			->subject('Test email from Symfony')
			->text('If you see this email — it means everything works ✅')
			->html('<p>HTML message</p>');

		try {
			$mailer->send($email);
		} catch (\Throwable $e) {
			return new Response('Ошибка при отправке: ' . $e->getMessage(), 500);
		}
		return new Response('Письмо успешно отправлено ✅');
	}
}
