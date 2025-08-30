<?php

namespace App\Controller;

use App\Repository\TreatmentsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
	#[Route('/', name: 'homepage')]
	public function index(TreatmentsRepository $tRepo): Response
	{
		$treatments = $tRepo->findAll();

		return $this->render('home/index.html.twig', [
			'treatments' => $treatments,
		]);
	}
}