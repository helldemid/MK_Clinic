<?php

// src/Controller/ServiceController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoriesRepository;
use App\Repository\TreatmentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Categories;

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

	#[Route('/treatments/category/{id}/delete', name: 'category_delete', methods: ['DELETE'])]
	public function deleteCategory(int $id, EntityManagerInterface $em): JsonResponse
	{
		try {
			// Deny access if the current user is not a SUPER_ADMIN
			$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

			$category = $em->getRepository(Categories::class)->find($id);

			if (!$category) {
				return $this->json(['success' => false, 'error' => 'Category not found', 'message' => 'Category not found'], 404);
			}

			$em->remove($category);
			$em->flush();

			return $this->json(['success' => true]);
		} catch (\Exception $e) {
			return $this->json(['success' => false, 'error' => $e->getMessage(), 'message' => 'Server error'], 500);
		}
	}

	#[Route('/treatments/category/{id}/edit', name: 'category_edit', methods: ['POST'])]
	public function editCategory(
		int $id,
		Request $request,
		EntityManagerInterface $em
	): JsonResponse {
		try {
			// Deny access if the current user is not a SUPER_ADMIN
			$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

			$category = $em->getRepository(Categories::class)->find($id);

			if (!$category) {
				return $this->json(['success' => false, 'error' => 'Category not found', 'message' => 'Category not found'], 404);
			}

			$data = json_decode($request->getContent(), true);
			$name = trim($data['name'] ?? '');

			if (empty($name)) {
				return $this->json(['success' => false, 'error' => 'Name cannot be empty', 'message' => 'Name cannot be empty'], 400);
			}

			$category->setName($name);
			$em->flush();

			return $this->json(['success' => true]);
		} catch (\Exception $e) {
			return $this->json(['success' => false, 'error' => $e->getMessage(), 'message' => 'Server error'], 500);
		}
	}

	#[Route('/treatments/category/create', name: 'treatment_category_create', methods: ['POST'])]
	public function create(
		Request $request,
		EntityManagerInterface $em,
		CategoriesRepository $repo
	): JsonResponse {
		try {
			// Deny access if the current user is not a SUPER_ADMIN
			$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

			$data = json_decode($request->getContent(), true);
			$name = trim($data['name'] ?? '');

			if (empty($name)) {
				return $this->json([
					'success' => false,
					'error' => 'Validation failed',
					'message' => 'Name cannot be empty'
				], 400);
			}

			// Проверяем уникальность
			$existing = $repo->findOneBy(['name' => $name]);
			if ($existing) {
				return $this->json([
					'success' => false,
					'error' => 'Validation failed',
					'message' => 'Category with this name already exists'
				], 400);
			}

			$category = new Categories();
			$category->setName($name);

			$em->persist($category);
			$em->flush();

			return $this->json([
				'success' => true,
				'category' => [
					'id' => $category->getId(),
					'name' => $category->getName()
				]
			]);
		} catch (\Throwable $e) {
			return $this->json([
				'success' => false,
				'message' => 'Server error',
				'errors' => [$e->getMessage()]
			], 500);
		}
	}

}
