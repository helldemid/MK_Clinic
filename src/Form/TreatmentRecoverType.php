<?php
namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TreatmentRecover;

class TreatmentRecoverType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('min', IntegerType::class, ['label' => 'Recovery from'])
			->add('max', IntegerType::class, ['label' => 'Recovery to'])
			->add('period', ChoiceType::class, [
				'label' => 'Recovery period',
				'choices' => array_combine(TreatmentRecover::RECOVER_PERIODS, TreatmentRecover::RECOVER_PERIODS),
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => TreatmentRecover::class,
		]);
	}
}