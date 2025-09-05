<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity]
#[UniqueEntity(fields: ["email"], message: "This email is already in use")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: "Email is required")]
    #[Assert\Email(message: "The email '{{ value }}' is not valid")]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Assert\NotBlank(message: "Password is required")]
    #[Assert\Length(
        min: 6,
        minMessage: "Password must be at least {{ limit }} characters long"
    )]
    #[Assert\Regex(
        pattern: "/[0-9]/",
        message: "Password must contain at least one number."
    )]
    #[Assert\Regex(
        pattern: "/[a-z]/",
        message: "Password must contain at least one lowercase letter."
    )]
    #[Assert\Regex(
        pattern: "/[A-Z]/",
        message: "Password must contain at least one uppercase letter."
    )]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank(message: "First name is required")]
    #[Assert\Length(
        min: 2,
        minMessage: "First name must be at least {{ limit }} characters long"
    )]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false)]
    #[Assert\NotBlank(message: "Last name is required")]
    #[Assert\Length(
        min: 2,
        minMessage: "Last name must be at least {{ limit }} characters long"
    )]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\+44\d{10}$/',
        message: 'Phone number must be in UK format: +44XXXXXXXXXX'
    )]
    private ?string $phone = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isActive = false;

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return $this->email; }

    public function getRoles(): array { return array_unique(array_merge($this->roles, ['ROLE_USER'])); }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }

    public function eraseCredentials(): void {}

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): self { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(?string $lastName): self { $this->lastName = $lastName; return $this; }

    public function getPhone(): ?string { return $this->phone; }

    public function setPhone(?string $phone): self {
        $this->phone = $phone;
        return $this;
    }

    public function isActive(): bool {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self {
        $this->isActive = $isActive;
        return $this;
    }

    public function getFullName(): string {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}