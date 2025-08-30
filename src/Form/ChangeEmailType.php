<?php
namespace App\Form;

use App\Dto\ChangeEmailDto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangeEmailType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('email', EmailType::class, [
				'label' => 'New email',
				'required' => true,
			])
			->add('confirmEmail', EmailType::class, [
				'label' => 'Repeat email',
				'required' => true,
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => ChangeEmailDto::class,
		]);
	}
}
