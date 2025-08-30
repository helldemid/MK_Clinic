<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class PhoneInput
{
	#[Assert\NotBlank(message: 'Phone is required.')]
	// Разрешаем либо UK-мобайл (+44 7XXX XXX XXX), либо общий E.164 (+XXXXXXXX)
	#[Assert\Regex(
		pattern: "/^(\+44\s?7\d{3}\s?\d{3}\s?\d{3})$/",
		message: "Invalid phone format."
	)]
	public ?string $phone = null;
}