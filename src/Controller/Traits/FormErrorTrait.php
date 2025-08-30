<?php

namespace App\Controller\Traits;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

trait FormErrorTrait
{
	protected function violationsToArray(ConstraintViolationListInterface $violations): array
	{
		$errors = [];
		foreach ($violations as $violation) {
			$path = $violation->getPropertyPath() ?: '_';
			$errors[$path] = $violation->getMessage();
		}
		return $errors;
	}
}