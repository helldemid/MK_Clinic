<?php

// src/Controller/ServiceController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TreatmentsRepository;
use App\Repository\TreatmentQuestionsRepository;

class TreatmentController extends AbstractController
{
	#[Route('/treatment/{id}_{slug}', name: 'treatment_show', requirements: ['id' => '\d+'])]
	public function show(int $id, string $slug, TreatmentsRepository $tRepo, TreatmentQuestionsRepository $tqRepo)
	{
		$treatment = $tRepo->getFullTreatmentData($id);
		if (!$treatment) {
			throw $this->createNotFoundException('Treatment not found');
		}
		$additionalInformation = $tqRepo->getAdditionalInformation((int) $treatment['id']);

		return $this->render('treatment/show.html.twig', [
			'treatment' => $treatment,
			'additionalInformation' => $additionalInformation,
		]);
	}
}
