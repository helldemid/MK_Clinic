<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactDetailItemType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('icon', ChoiceType::class, [
				'label' => 'Icon',
				'choices' => $options['icon_choices'],
				'placeholder' => 'Select icon',
				'constraints' => [
					new NotBlank(),
				],
				'choice_translation_domain' => false,
			])
			->add('label', TextType::class, [
				'label' => 'Text',
				'constraints' => [
					new NotBlank(),
				],
			])
			->add('url', TextType::class, [
				'label' => 'Link / href',
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
			'icon_choices' => [],
		]);
		$resolver->setAllowedTypes('icon_choices', 'array');
	}
}
