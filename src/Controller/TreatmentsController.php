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
use App\Entity\Treatments;
use App\Entity\TreatmentsShortInfo;
use App\Entity\TreatmentTime;
use App\Entity\TreatmentRecover;
use App\Entity\TreatmentPrice;
use App\Entity\TreatmentQuestions;
use App\Entity\PopularTreatments;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use App\Form\TreatmentType;
use App\Controller\Traits\StatusRenderTrait;

class TreatmentsController extends AbstractController
{
	use StatusRenderTrait;
	#[Route('/treatments', name: 'treatments')]
	public function index(Request $request, CategoriesRepository $categoryRepo, TreatmentsRepository $treatmentRepo)
	{
		$categories = $categoryRepo->findAll();

		$categoryId = (int) $request->query->get('category', 0);
		$treatments = $treatmentRepo->getTreatmentsDataForCards($categoryId, 1);

		return $this->render('treatments/index.html.twig', [
			'categories' => $categories,
			'treatments' => $treatments,
			'selectedCategory' => $categoryId,
			'isEditor' => $this->isGranted('ROLE_SUPER_ADMIN'),
		]);
	}

	#[Route('/treatments/filter', name: 'treatments_filter', methods: ['POST'])]
	public function filter(Request $request, TreatmentsRepository $treatmentRepo)
	{
		$categoryId = (int) $request->request->get('category_id', 0);
		$activity = (int) $request->request->get('activity', 1);

		$treatments = $treatmentRepo->getTreatmentsDataForCards($categoryId, $activity);

		$html = $this->renderView('treatments/_treatment_cards.html.twig', [
			'treatments' => $treatments,
			'isEditor' => $this->isGranted('ROLE_SUPER_ADMIN'),
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


	#[Route('/treatment/{id}/toggle', name: 'admin_treatment_toggle', methods: ['PUT'])]
	public function toggle(Treatments $treatment, EntityManagerInterface $em): JsonResponse
	{
		// Deny access if the current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
		try {
			$treatment->setIsActive(!$treatment->isActive());
			$em->flush();

			return $this->json([
				'success' => true,
				'isActive' => $treatment->isActive(),
			]);
		} catch (\Throwable $e) {
			return $this->json([
				'success' => false,
				'error' => $e->getMessage(),
			], 500);
		}
	}

	#[Route('/treatment/{id}/popular', name: 'admin_treatment_popular', methods: ['PUT'])]
	public function togglePopular(Treatments $treatment, EntityManagerInterface $em): JsonResponse
	{
		// Deny access if the current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
		try {
			$response = false;
			$treatmentId = $treatment->getId();
			$em->flush();

			$popularTreatment = $em->getRepository(PopularTreatments::class)->findOneBy(['treatment' => $treatmentId]);
			if ($popularTreatment) {
				$em->remove($popularTreatment);
			} else {
				$newPopular = new PopularTreatments();
				$newPopular->setTreatment($treatment);
				$em->persist($newPopular);
				$response = true;
			}
			$em->flush();

			return $this->json([
				'success' => true,
				'isPopular' => $response
			]);
		} catch (\Throwable $e) {
			return $this->json([
				'success' => false,
				'error' => $e->getMessage(),
			], 500);
		}
	}

	#[Route('/treatment/new', name: 'treatment_new')]
	#[Route('/treatment/{id}/edit', name: 'treatment_edit')]
	public function form(Request $request, EntityManagerInterface $em, Treatments $treatment = null)
	{
		// Deny access if the current user is not a SUPER_ADMIN
		$this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
		$isEdit = $treatment !== null;

		if (!$treatment) {
			$treatment = new Treatments();
		}

		$shortInfo = $em->getRepository(TreatmentsShortInfo::class)
			->findOneBy(['treatment' => $treatment]) ?? new TreatmentsShortInfo();

		$recover = $treatment->getRecover() ?? new TreatmentRecover();
		$time = $treatment->getTime() ?? new TreatmentTime();
		$price = $treatment->getPrice() ?? new TreatmentPrice();

		$questions = $treatment->getId()
			? $em->getRepository(TreatmentQuestions::class)->findBy(['treatment' => $treatment])
			: [];

		// Подготовка категорий для select
		$categories = $em->getRepository(Categories::class)->findAll();

		$form = $this->createForm(TreatmentType::class, $treatment, [
			'categories' => $categories,
			'shortInfo' => $shortInfo,
			'recover' => $recover,
			'time' => $time,
			'price' => $price,
			'questions' => $questions,
		]);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			// --- Файлы ---
			$cardFile = $form->get('image_card')->getData();
			$pageFile = $form->get('image_page')->getData();

			if ($cardFile || $pageFile) {
				// Берем текущее имя или генерируем новое
				$filename = $treatment->getImageName();
				if (!$filename || $filename === 'placeholder.png') {
					$extension = ($cardFile ?: $pageFile)->guessExtension() ?: 'bin';
					$filename = uniqid() . '.' . $extension;
					$treatment->setImageName($filename);
				}

				try {
					if ($cardFile) {
						$cardFile->move($this->getParameter('treatment_card_dir'), $filename);
					}
					if ($pageFile) {
						$pageFile->move($this->getParameter('treatment_page_dir'), $filename);
					}
				} catch (FileException $e) {
					$this->addFlash('error', 'Image upload failed. Please try again.');
					$this->addFlash('errorDev', $e->getMessage());

					// Возвращаем форму обратно с ошибкой
					return $this->render('treatment/form.html.twig', [
						'form' => $form->createView(),
						'isEdit' => $isEdit,
					]);
				}
			} else if (!$isEdit && !$treatment->getImageName()) {
				// Для новых объектов без файлов ставим плейсхолдер
				$treatment->setImageName('placeholder.jpg');
			}


			// --- Подформы ---
			foreach (['recover', 'time', 'price'] as $subform) {
				$data = $form->get($subform)->getData();
				if ($data) {
					$setter = 'set' . ucfirst($subform);
					$treatment->$setter($data);
				}
			}

			$cardData = $form->get('shortInfo')->getData();
			if ($cardData) {
				$cardData->setTreatment($treatment); // если есть связь
				$em->persist($cardData);
			}

			// Удаляем все старые вопросы
			$oldQuestions = $em->getRepository(TreatmentQuestions::class)->findBy(['treatment' => $treatment]);
			foreach ($oldQuestions as $oldQ) {
				$em->remove($oldQ);
			}

			// --- Вопросы ---
			$questionsData = $form->get('questions')->getData(); // если в форме есть коллекция
			foreach ($questionsData as $qData) {
				$question = new TreatmentQuestions();
				$question->setTreatment($treatment);
				$question->setQuestion($qData->getQuestion());
				$question->setAnswer($qData->getAnswer());
				$em->persist($question);
			}

			$em->persist($treatment->getRecover());
			$em->persist($treatment->getPrice());
			$em->persist($treatment->getTime());
			$em->persist($treatment);
			$em->flush();

			$this->addFlash('success', $isEdit ? 'Treatment updated!' : 'Treatment created!');

			return $this->renderStatus('Success', $isEdit ? 'Treatment updated!' : 'Treatment created!', 1);
		}

		return $this->render('treatment/form.html.twig', [
			'form' => $form->createView(),
			'isEdit' => $isEdit,
		]);
	}

}
