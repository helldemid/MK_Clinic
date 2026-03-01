<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactIconBlockType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options): void
	{
		$builder
			->add('title', TextType::class, [
				'label' => 'Block title',
				'constraints' => [
					new NotBlank(),
				],
			])
			->add('items', CollectionType::class, [
				'label' => 'Icons',
				'entry_type' => ContactIconItemType::class,
				'entry_options' => [
					'icon_choices' => $options['icon_choices'],
				],
				'allow_add' => true,
				'allow_delete' => true,
				'by_reference' => false,
				'prototype' => true,
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
