<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Treatments;
use App\Form\TreatmentQuestionType;
use App\Form\TreatmentShortInfoType;
use App\Form\TreatmentRecoverType;
use App\Form\TreatmentTimeType;
use App\Form\TreatmentPriceType;

class TreatmentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$builder
			->add('name', TextType::class, [
				'label' => 'Treatment name'
			])
			->add('category', ChoiceType::class, [
				'choices' => $options['categories'],
				'choice_label' => 'name',
				'choice_value' => 'id',
				'label' => 'Category',
			])
			->add('image_card', FileType::class, [
				'label' => 'Card image',
				'mapped' => false,
				'required' => false,
			])
			->add('image_page', FileType::class, [
				'label' => 'Page image',
				'mapped' => false,
				'required' => false,
			])
			->add('shortInfo', TreatmentShortInfoType::class, [
				'label' => false,
				'required' => false,
				'mapped' => false,
				'data' => $options['shortInfo'] ?? null,
			])
			->add('recover', TreatmentRecoverType::class, [
				'label' => false,
				'required' => false,
				'mapped' => false,
				'data' => $options['recover'] ?? null,
			])
			->add('time', TreatmentTimeType::class, [
				'label' => false,
				'required' => false,
				'mapped' => false,
				'data' => $options['time'] ?? null,
			])
			->add('price', TreatmentPriceType::class, [
				'label' => false,
				'required' => false,
				'mapped' => false,
				'data' => $options['price'] ?? null,
			])
			->add('questions', CollectionType::class, [
				'entry_type' => TreatmentQuestionType::class,
				'allow_add' => true,
				'allow_delete' => true,
				'by_reference' => false,
				'label' => null,
				'required' => false,
				'mapped' => false,
				'data' => $options['questions'] ?? [],
			])
			->add('fullDescription', TextareaType::class, [
				'label' => 'Full description',
				'required' => false,
				'attr' => [
					'class' => 'form-control',
					'placeholder' => 'Enter full description'
				]
			])
			->add('discomfortLevel', ChoiceType::class, [
				'label' => 'Discomfort level',
				'choices' => [
					'None' => 0,
					'Low' => 1,
					'Medium' => 2,
					'High' => 3,
					'Very High' => 4,
					'Extremely Hight' => 5,
				],
				'placeholder' => 'Select discomfort level',
				'required' => false,
				'attr' => [
					'class' => 'form-control'
				]
			]);
		;
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Treatments::class,
			'categories' => [],
			'shortInfo' => null,
			'recover' => null,
			'time' => null,
			'price' => null,
			'questions' => [],
		]);
	}
}
