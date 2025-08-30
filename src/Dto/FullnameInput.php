<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class FullnameInput
{
	#[Assert\NotBlank(message: 'First name is required.')]
	#[Assert\Length(min: 2, max: 50, minMessage: 'Too short.', maxMessage: 'Too long.')]
	#[Assert\Regex(
		pattern: "/^[\p{L}\p{M}][\p{L}\p{M}\s'\-]{1,49}$/u",
		message: "Only letters, spaces, apostrophes and hyphens are allowed."
	)]
	public ?string $firstName = null;

	#[Assert\NotBlank(message: 'Last name is required.')]
	#[Assert\Length(min: 2, max: 50, minMessage: 'Too short.', maxMessage: 'Too long.')]
	#[Assert\Regex(
		pattern: "/^[\p{L}\p{M}][\p{L}\p{M}\s'\-]{1,49}$/u",
		message: "Only letters, spaces, apostrophes and hyphens are allowed."
	)]
	public ?string $lastName = null;
}