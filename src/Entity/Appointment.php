<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Appointment
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	// --- Пациент (зарегистрированный) ---
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
	private ?User $user = null;

	// --- Пациент-гость ---
	#[ORM\ManyToOne(targetEntity: AppointmentGuestContact::class, cascade: ['persist'])]
	#[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
	private ?AppointmentGuestContact $guestContact = null;

	// --- Дата приёма ---
	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $appointmentDate;

	// --- Услуга ---
	#[ORM\ManyToOne(targetEntity: Treatments::class)]
	#[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
	private Treatments $treatment;

	// --- Врач (опционально) ---
	#[ORM\Column(length: 150, nullable: true)]
	private ?string $doctor = null;

	// --- Статус ---
	#[ORM\Column(length: 50)]
	#[Assert\Choice(choices: ['scheduled', 'cancelled', 'confirmed', 'no_show', 'completed'], message: 'Invalid status.')]
	private string $status = 'scheduled';
	// возможные значения: scheduled, cancelled, no_show, completed

	// --- Кто создал запись ---
	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
	private ?User $createdBy = null;

	// --- Дата создания ---
	#[ORM\Column(type: 'datetime')]
	private \DateTimeInterface $createdAt;

	public function __construct()
	{
		$this->createdAt = new \DateTimeImmutable();
	}

	// --- Getters / Setters ---
	public function getId(): ?int
	{
		return $this->id;
	}

	public function getUser(): ?User
	{
		return $this->user;
	}
	public function setUser(?User $user): self
	{
		$this->user = $user;
		return $this;
	}

	public function getGuestContact(): ?AppointmentGuestContact
	{
		return $this->guestContact;
	}
	public function setGuestContact(?AppointmentGuestContact $guestContact): self
	{
		$this->guestContact = $guestContact;
		return $this;
	}

	public function getAppointmentDate(): \DateTimeInterface
	{
		return $this->appointmentDate;
	}
	public function setAppointmentDate(\DateTimeInterface $date): self
	{
		$this->appointmentDate = $date;
		return $this;
	}

	public function getTreatment(): Treatments
	{
		return $this->treatment;
	}
	public function setTreatment(Treatments $treatment): self
	{
		$this->treatment = $treatment;
		return $this;
	}

	public function getDoctor(): ?string
	{
		return $this->doctor;
	}
	public function setDoctor(?string $doctor): self
	{
		$this->doctor = $doctor;
		return $this;
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

	public function getCreatedBy(): ?User
	{
		return $this->createdBy;
	}
	public function setCreatedBy(?User $user): self
	{
		$this->createdBy = $user;
		return $this;
	}

	public function getCreatedAt(): \DateTimeInterface
	{
		return $this->createdAt;
	}

	// --- Helper: вывод имени пациента ---
	public function getDisplayPatientName(): string
	{
		if ($this->user) {
			return $this->user->getFullName();
		}
		if ($this->guestContact) {
			return $this->guestContact->getName();
		}
		return '-';
	}
}
