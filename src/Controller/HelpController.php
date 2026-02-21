<?php

namespace App\Controller;

use App\Repository\HelpSectionRepository;
use App\Entity\HelpSection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
	#[Route('/help/{slug}', name: 'help', defaults: ['slug' => null])]
	public function index(?string $slug, HelpSectionRepository $helpSectionRepository): Response
	{
		$sections = $helpSectionRepository->findAllOrdered();
		$default = $slug !== null
			? $helpSectionRepository->findBySlug($slug)
			: $helpSectionRepository->findFirst();

		if (empty($default))
			throw $this->createNotFoundException('Help section not found.');

		return $this->render('help/index.html.twig', [
			'sections' => $sections,
			'default' => $this->renderSectionContent($default),
			'activePosition' => $default->getPosition()
		]);
	}

	#[Route('/help/load/{slug}', name: 'help_load', methods: ['GET'])]
	public function load(string $slug, HelpSectionRepository $repo): JsonResponse
	{
		$section = $repo->findBySlug($slug);

		if (!$section) {
			return new JsonResponse(['error' => 'Not found'], 404);
		}

		return new JsonResponse([
			'title' => $section->getTitle(),
			'content' => $this->renderSectionContent($section),
		]);
	}

	private function renderSectionContent(HelpSection $section): string
	{
		return $this->renderView('help/_section_content.html.twig', [
			'helpSection' => $section,
		]);
	}

}
