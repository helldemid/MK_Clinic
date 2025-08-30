<?php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeEmailDto
{
	#[Assert\NotBlank]
	#[Assert\Email(message: 'Please enter a valid email.')]
	public ?string $email = null;

	#[Assert\NotBlank]
	#[Assert\Email(message: 'Please enter a valid email.')]
	#[Assert\EqualTo(propertyPath: 'email', message: 'Emails do not match.')]
	public ?string $confirmEmail = null;
}