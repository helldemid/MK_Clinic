<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TreatmentPrice;

class TreatmentPriceType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('isFixed', CheckboxType::class, [
				'label' => 'Fixed price?',
				'required' => false,
			])
			->add('price', IntegerType::class, ['label' => 'Price'])
			->add('priceType', ChoiceType::class, [
				'label' => 'Price type',
				'choices' => [
					'Unset' => 'unset',
					'Course' => 'course',
					'Session' => 'session',
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => TreatmentPrice::class,
		]);
	}
}