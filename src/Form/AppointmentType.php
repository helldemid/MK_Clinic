<?php

namespace App\Form;

use App\Entity\Appointment;
use App\Entity\AppointmentGuestContact;
use App\Entity\User;
use App\Entity\Treatments;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class AppointmentType extends AbstractType
{
	public function buildForm(FormBuilderInterface $builder, array $options)
	{
		$appointment = $builder->getData();
		$guestContact = $appointment->getGuestContact();
		$selectedUser = $appointment->getUser();
		$userChoices = [];
		if ($selectedUser) {
			$userChoices = [$selectedUser];
		}
		$builder
			// choice: registered user or guest
			->add('patientType', ChoiceType::class, [
				'mapped' => false,
				'label' => 'Patient Type',
				'choices' => [
					'Registered User' => 'user',
					'Guest' => 'guest',
				],
				'expanded' => true,
				'required' => true,
				'data' => $options['patientType'] ?? 'user',
			])
			// Date and time
			->add('appointmentDate', DateTimeType::class, [
				'widget' => 'single_text',
				'label' => 'Date and time',
			])
			// Service
			->add('treatment', EntityType::class, [
				'class' => Treatments::class,
				'choice_label' => 'name',
				'label' => 'Treatment',
			])
			->add('doctor', TextType::class, [
				'required' => true,
				'label' => 'Doctor',
				'constraints' => [
					new Assert\Length(['min' => 2, 'max' => 100]),
					new Assert\NotBlank(),
				],
			])
			// Registered user (autocomplete)
			->add('user', EntityType::class, [
				'class' => User::class,
				'choice_label' => 'fullName',
				'label' => 'Select user',
				'required' => false,
				'choices' => $userChoices,
				'attr' => [
					'class' => 'autocomplete-user', // здесь можно подключить JS autocomplete
				],
			])
			// Guest contact details
			->add('guestName', TextType::class, [
				'mapped' => false,
				'required' => false,
				'label' => 'Guest name',
				'data' => $guestContact ? $guestContact->getName() : null,
			])
			->add('guestEmail', TextType::class, [
				'mapped' => false,
				'required' => false,
				'label' => 'Guest email',
				'data' => $guestContact ? $guestContact->getEmail() : null,
			])
			->add('guestPhone', TextType::class, [
				'mapped' => false,
				'required' => false,
				'label' => 'Guest phone',
				'data' => $guestContact ? $guestContact->getPhone() : null,
			])
		;

		// Listen for form submission to adjust fields based on patientType
		$builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
			$data = $event->getData();
			$form = $event->getForm();

			if (!isset($data['patientType'])) {
				return;
			}

			if ($data['patientType'] === 'guest') {
				// Make guest fields required
				$form->add('guestName', TextType::class, [
					'mapped' => false,
					'required' => true,
					'label' => 'Guest name',
					'constraints' => [
						new Assert\Length(['min' => 2, 'max' => 100]),
						new Assert\NotBlank(),
					],
				]);
				$form->add('guestEmail', TextType::class, [
					'mapped' => false,
					'required' => true,
					'label' => 'Guest email',
					'constraints' => [
						new Assert\Email(),
						new Assert\NotBlank(),
					],
				]);
				$form->add('guestPhone', TextType::class, [
					'mapped' => false,
					'required' => true,
					'label' => 'Guest phone',
					'constraints' => [
						new Assert\Regex([
							'pattern' => '/^\+44\s?7\d{3}\s?\d{3}\s?\d{3}$/',
							'message' => 'Phone number must be in UK format: +44XXXXXXXXXX',
						]),
						new Assert\NotBlank(),
					],
				]);
				$data['user'] = null;

				$guest = new AppointmentGuestContact();
				$guest->setName($data['guestName'] ?? '');
				$guest->setEmail($data['guestEmail'] ?? null);
				$guest->setPhone($data['guestPhone'] ?? null);

				$event->getForm()->getData()->setGuestContact($guest);
				$event->setData($data);
			} else {
				// If user is selected, make it required
				$form->add('user', EntityType::class, [
					'class' => User::class,
					'choice_label' => 'fullName',
					'label' => 'Select user',
					'required' => true,
					'constraints' => [
						new Assert\NotBlank(['message' => 'Please select a user.']),
					],
					'attr' => [
						'class' => 'autocomplete-user',
					],
				]);
				$event->getForm()->getData()->setGuestContact(null);
			}
		});
	}

	public function configureOptions(OptionsResolver $resolver)
	{
		$resolver->setDefaults([
			'data_class' => Appointment::class,
			'patientType' => 'user',
		]);
	}
}
