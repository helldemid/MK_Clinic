<?php

namespace App\Controller;

use App\Repository\HelpSectionRepository;
use App\Entity\HelpSection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelpController extends AbstractController
{
	private EntityManagerInterface $em;

	public function __construct(EntityManagerInterface $em)
	{
		$this->em = $em;
	}

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
			'default' => $default->getContent(),
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
			'content' => $section->getContent()
		]);
	}

}
