<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\TreatmentQuestions;

class TreatmentQuestionType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('question', TextType::class, [
				'label' => 'Question',
				'attr' => ['class' => 'full', 'placeholder' => 'Enter the question here...']
			])
			->add('answer', TextareaType::class, [
				'label' => 'Answer',
				'attr' => ['class' => 'full', 'placeholder' => 'Enter the answer here...']
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => TreatmentQuestions::class,
		]);
	}
}