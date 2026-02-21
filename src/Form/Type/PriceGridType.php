<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PriceGridType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder->addModelTransformer(new CallbackTransformer(
			static function ($modelData): string {
				if (!is_array($modelData)) {
					return json_encode(['columns' => [], 'rows' => []], JSON_THROW_ON_ERROR);
				}

				return json_encode($modelData, JSON_THROW_ON_ERROR);
			},
			static function ($submittedData): array {
				if (!is_string($submittedData) || trim($submittedData) === '') {
					return ['columns' => [], 'rows' => []];
				}

				try {
					$decoded = json_decode($submittedData, true, flags: JSON_THROW_ON_ERROR);
				} catch (\JsonException) {
					return ['columns' => [], 'rows' => []];
				}

				if (!is_array($decoded)) {
					return ['columns' => [], 'rows' => []];
				}

				return $decoded;
			},
		));
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'required' => false,
		]);
	}

	public function getParent(): string
	{
		return HiddenType::class;
	}

	public function getBlockPrefix(): string
	{
		return 'price_grid';
	}
}
