<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegistrationController extends AbstractController
{
	#[Route('/register', name: 'app_register')]
	public function register(
		Request $request,
		EntityManagerInterface $em,
		UserPasswordHasherInterface $passwordHasher,
	): Response {
		$user = new User();

		$registrationForm = $this->createFormBuilder($user)
			->add('firstName', TextType::class, [
				'attr' => ['class' => 'form-control', 'placeholder' => 'First Name']
			])
			->add('lastName', TextType::class, [
				'attr' => ['class' => 'form-control', 'placeholder' => 'Last Name']
			])
			->add('email', EmailType::class, [
				'attr' => ['class' => 'form-control', 'placeholder' => 'Email address']
			])
			->add('password', PasswordType::class, [
				'attr' => ['class' => 'form-control', 'placeholder' => 'Password']
			])
			->add('register', SubmitType::class, [
				'label' => 'Sign Up',
			])
			->getForm();

		$registrationForm->handleRequest($request);

		if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
			$plainPassword = $registrationForm->get('password')->getData();
			$hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
			$user->setPassword($hashedPassword);
			$user->setRoles(['ROLE_USER']);

			$em->persist($user);
			$em->flush();

			return $this->redirectToRoute('app_create_account', [
				'name' => $user->getFirstName(),
				'email' => $user->getEmail(),
				'user_id' => $user->getId(),
			]);
		}

		return $this->render('registration/register.html.twig', [
			'registrationForm' => $registrationForm->createView(),
		]);
	}
}