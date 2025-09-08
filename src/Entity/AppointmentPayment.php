<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class AppointmentPayment
{

	public const PAYMENT_STATUSES = [
		'not_paid' => 'Not Paid',
		'paid' => 'Paid',
		'refunded' => 'Refunded',
	];

	public const PAYMENT_METHODS = [
		'cash' => 'Cash',
		'card' => 'Card',
	];

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 20)]
	#[Assert\Choice(choices: ['not_paid', 'paid', 'refunded'], message: 'Invalid status.')]
	private string $status = 'not_paid';

	#[ORM\Column(length: 20)]
	#[Assert\Choice(choices: ['cash', 'card'], message: 'Invalid method.')]
	private string $method = 'cash';

	#[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
	private float $amount = 0.0;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getStatus(): string
	{
		return $this->status;
	}

	public function setStatus(string $status): self
	{
		$this->status = $status;
		return $this;
	}

	public function getMethod(): string
	{
		return $this->method;
	}

	public function setMethod(string $method): self
	{
		$this->method = $method;
		return $this;
	}

	public function getAmount(): float
	{
		return $this->amount;
	}

	public function setAmount(float $amount): self
	{
		$this->amount = $amount;
		return $this;
	}
}
