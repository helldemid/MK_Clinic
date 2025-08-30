<?php

namespace App\Entity;

use App\Repository\TreatmentTimeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TreatmentTimeRepository::class)]
class TreatmentTime
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $hours = null;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Assert\LessThanOrEqual(59, message: 'Can not be more than 59 minutes')]
    private ?int $minutes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHours(): ?int
    {
        return $this->hours;
    }

    public function setHours(?int $hours): static
    {
        $this->hours = $hours;

        return $this;
    }

    public function getMinutes(): ?int
    {
        return $this->minutes;
    }

    public function setMinutes(int $minutes): static
    {
        $this->minutes = $minutes;

        return $this;
    }
}
