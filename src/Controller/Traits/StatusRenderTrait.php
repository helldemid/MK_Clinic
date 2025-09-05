<?php

namespace App\Controller\Traits;

use Symfony\Component\HttpFoundation\Response;

trait StatusRenderTrait
{
	/**
	 * Renders a status message.
	 *
	 * @param string $title
	 * @param string $message
	 * @param integer $status (1 - success, 0 - failure)
	 * @return Response
	 */
	private function renderStatus(string $title, string $message, int $status = 1): Response
	{
		$statusTemplate = $status === 1 ? 'confirm_success.html.twig' : 'confirm_fail.html.twig';
		return $this->render('messages/' . $statusTemplate, [
			'title' => $title,
			'message' => $message,
		]);
	}
}