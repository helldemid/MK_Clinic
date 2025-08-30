<?php

// src/Controller/ServiceController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\TreatmentsRepository;

class TreatmentsController extends AbstractController
{
	#[Route('/treatments', name: 'treatments')]
	public function index(Request $request, CategoriesRepository $categoryRepo, TreatmentsRepository $treatmentRepo)
	{
		$categories = $categoryRepo->findAll();

		$categoryId = (int) $request->query->get('category', 0);
		$treatments = $treatmentRepo->getTreatmentsDataForCards($categoryId);

		return $this->render('treatments/index.html.twig', [
			'categories' => $categories,
			'treatments' => $treatments,
			'selectedCategory' => $categoryId
		]);
	}

	#[Route('/treatments/filter', name: 'treatments_filter', methods: ['POST'])]
	public function filter(Request $request, TreatmentsRepository $treatmentRepo)
	{
		$categoryId = (int) $request->request->get('category_id', 0);

		$treatments = $treatmentRepo->getTreatmentsDataForCards($categoryId);

		$html = $this->renderView('treatments/_treatment_cards.html.twig', [
			'treatments' => $treatments,
		]);

		return new JsonResponse(['html' => $html]);
	}
}
