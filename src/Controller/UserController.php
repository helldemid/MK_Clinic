<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
	#[Route('/user/autocomplete', name: 'user_autocomplete')]
	public function autocomplete(Request $request, UserRepository $userRepository): JsonResponse
	{
		$term = $request->query->get('q');
		$users = $userRepository->findByTerm($term);

		$items = [];
		foreach ($users as $user) {
			$items[] = [
				'id' => $user->getId(),
				'text' => $user->getId().', '.$user->getFullName(),
			];
		}

		return new JsonResponse(['items' => $items]);
	}
}