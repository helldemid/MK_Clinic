<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ChangePasswordType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('password', PasswordType::class, [
				'label' => 'New password',
				'required' => true,
				'constraints' => [
					new Assert\NotBlank(),
					new Assert\Length(['min' => 6]),
					new Assert\Regex([
						'pattern' => '/[0-9]/',
						'message' => 'Password must contain at least one number.',
					]),
					new Assert\Regex([
						'pattern' => '/[a-z]/',
						'message' => 'Password must contain at least one lowercase letter.',
					]),
					new Assert\Regex([
						'pattern' => '/[A-Z]/',
						'message' => 'Password must contain at least one uppercase letter.',
					]),
				],
			])
			->add('confirm_password', PasswordType::class, [
				'label' => 'Repeat password',
				'required' => true,
				'mapped' => false,
				'constraints' => [
					new Assert\NotBlank(),
				],
			]);

		// Пост-валидация на совпадение паролей
		$builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
			$form = $event->getForm();
			$data = $form->getData();
			$password = $data['password'] ?? null;
			$confirm = $form->get('confirm_password')->getData();

			if ($password !== $confirm) {
				$form->get('confirm_password')->addError(
					new \Symfony\Component\Form\FormError('Passwords do not match.')
				);
			}
		});
	}
}