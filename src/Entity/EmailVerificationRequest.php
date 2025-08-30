<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'email_verification_request')]
class EmailVerificationRequest
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer', name: 'id')]
	private ?int $id = null;

	#[ORM\Column(type: 'string', length: 90, unique: true, nullable: true)]
	private ?string $email = null;

	#[ORM\Column(type: 'string', length: 32)]
	private string $code;

	#[ORM\Column(type: 'datetime', name: 'updated_at')]
	private \DateTimeInterface $updatedAt;

	#[ORM\Column(type: 'integer', name: 'action', options: ['comment' => '0 - undefined, 1 - forgot password, 2 - create account, 3 - change email, 4 - change password'])]
	private int $action;

	#[ORM\ManyToOne(targetEntity: User::class)]
	#[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
	private User $user;

	// Getters and setters...

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getEmail(): ?string
	{
		return $this->email;
	}
	public function setEmail(?string $email): self
	{
		$this->email = $email;
		return $this;
	}

	public function getCode(): string
	{
		return $this->code;
	}
	public function setCode(string $code): self
	{
		$this->code = $code;
		return $this;
	}

	public function getUpdatedAt(): \DateTimeInterface
	{
		return $this->updatedAt;
	}
	public function setUpdatedAt(\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;
		return $this;
	}

	public function getAction(): int
	{
		return $this->action;
	}
	public function setAction(int $action): self
	{
		$this->action = $action;
		return $this;
	}

	public function getUser(): User
	{
		return $this->user;
	}
	public function setUser(User $user): self
	{
		$this->user = $user;
		return $this;
	}
}