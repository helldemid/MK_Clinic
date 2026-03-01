<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class FooterNavigationLinkItemType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('sourceKey', ChoiceType::class, [
				'label' => 'Link source',
				'choices' => $options['link_choices'],
				'placeholder' => 'Select a link',
				'constraints' => [
					new NotBlank(),
				],
				'choice_translation_domain' => false,
			])
			->add('label', TextType::class, [
				'label' => 'Custom label (optional)',
				'required' => false,
				'empty_data' => '',
			])
			->add('position', HiddenType::class, [
				'required' => false,
				'empty_data' => '0',
			]);
	}

	public function configureOptions(OptionsResolver $resolver): void
	{
		$resolver->setDefaults([
			'data_class' => null,
			'link_choices' => [],
		]);
		$resolver->setAllowedTypes('link_choices', 'array');
	}
}
