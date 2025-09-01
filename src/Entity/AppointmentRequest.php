<?php
namespace App\Entity;

use App\Entity\User;
use App\Entity\Treatment;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class AppointmentRequest
{
	#[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
	private int $id;

	#[ORM\Column(type: 'string', length: 255)]
	#[Assert\NotBlank(message: 'Name is required.')]
	private string $name;

	#[ORM\Column(type: 'string', length: 255, nullable: true)]
	#[Assert\Email(message: 'Invalid email address.')]
	private ?string $email = null;

	#[ORM\Column(type: 'string', length: 20)]
	#[Assert\NotBlank(message: 'Phone is required.')]
	#[Assert\Regex(
		pattern: '/^\+44\s?7\d{3}\s?\d{3}\s?\d{3}$/',
		message: 'Phone number must be in UK format: +44XXXXXXXXXX'
	)]
	private string $phone;

	// Связь с процедурой (nullable, т.к. вопрос может быть без процедуры)
	#[ORM\ManyToOne(targetEntity: Treatments::class)]
	#[ORM\JoinColumn(nullable: true)]
	private ?Treatments $treatment = null;

	#[ORM\Column(type: 'text', nullable: true)]
	#[Assert\Length(
		max: 150,
		maxMessage: 'Question must be no longer than 150 characters.'
	)]
	#[Assert\Regex(
		pattern: '/^(?!.*<.*?>)(?!.*https?:\/\/).*$/i',
		message: 'Links and HTML are not allowed in the question.'
	)]
	private ?string $question = null;

	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $createdAt;

	// Связь с пользователем, если авторизован
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: true)]
	private ?User $user = null;

	#[ORM\Column(type: 'string', length: 32, options: ['default' => 'created'])]
	#[Assert\Choice(choices: ['created', 'in_progress', 'confirmed', 'no_answer', 'cancelled', 'done'], message: 'Invalid status.')]
	private string $status = 'created';

	public function __construct()
	{
		$this->createdAt = new \DateTime();
	}

	// --- геттеры и сеттеры ---
	public function setUser(?User $user): self
	{
		$this->user = $user;
		return $this;
	}
	public function getUser(): ?User
	{
		return $this->user;
	}

	public function setTreatment(?Treatments $treatment): self
	{
		$this->treatment = $treatment;
		return $this;
	}
	public function getTreatment(): ?Treatments
	{
		return $this->treatment;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}
	public function getName(): string
	{
		return $this->name;
	}

	public function setEmail(?string $email): self
	{
		$this->email = $email;
		return $this;
	}
	public function getEmail(): ?string
	{
		return $this->email;
	}

	public function setPhone(string $phone): self
	{
		$this->phone = $phone;
		return $this;
	}
	public function getPhone(): string
	{
		return $this->phone;
	}

	public function setQuestion(?string $question): self
	{
		$this->question = $question;
		return $this;
	}
	public function getQuestion(): ?string
	{
		return $this->question;
	}

	public function getCreatedAt(): \DateTimeInterface
	{
		return $this->createdAt;
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
}
