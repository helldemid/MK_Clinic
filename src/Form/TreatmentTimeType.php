<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TreatmentTime;

class TreatmentTimeType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('hours', IntegerType::class, ['label' => 'Duration (hours)'])
			->add('minutes', IntegerType::class, ['label' => 'Duration (minutes)']);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => TreatmentTime::class,
		]);
	}
}