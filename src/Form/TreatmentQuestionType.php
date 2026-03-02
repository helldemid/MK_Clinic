<?php
namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
			->add('answer', CKEditorType::class, [
				'label' => 'Answer',
				'config_name' => 'help_section',
				'autoload' => false,
				'attr' => [
					'class' => 'full js-treatment-answer-editor',
					'placeholder' => 'Enter the answer here...',
					'rows' => 8,
				]
			]);
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => TreatmentQuestions::class,
		]);
	}
}
