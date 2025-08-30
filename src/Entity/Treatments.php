<?php

namespace App\Entity;

use App\Repository\TreatmentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TreatmentsRepository::class)]
class Treatments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $imageName;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\Positive]
    #[Assert\LessThanOrEqual(5, message: 'Can not be more than 5')]
    #[Assert\GreaterThanOrEqual(0, message: 'Value cannot be less than 0')]
    private ?int $discomfortLevel = null;

    #[ORM\Column(length: 255)]
    private string $fullDescription;

    #[ORM\ManyToOne(targetEntity: Categories::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category = null;

    #[ORM\ManyToOne(targetEntity: TreatmentRecover::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?TreatmentRecover $recover = null;

    #[ORM\ManyToOne(targetEntity: TreatmentPrice::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?TreatmentPrice $price = null;

    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getImageName(): string
    {
        return $this->imageName;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getRecover(): ?TreatmentRecover
    {
        return $this->recover;
    }

    public function setRecover(?TreatmentRecover $recover): self
    {
        $this->recover = $recover;
        return $this;
    }

    public function getPrice(): ?TreatmentPrice
    {
        return $this->price;
    }

    public function setPrice(?TreatmentPrice $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getDiscomfortLevel(): ?int
    {
        return $this->discomfortLevel;
    }

    public function setDiscomfortLevel(?int $discomfortLevel): self
    {
        $this->discomfortLevel = $discomfortLevel;
        return $this;
    }

        public function getFullDescription(): ?int
    {
        return $this->fullDescription;
    }

    public function setFullDescription(?string $fullDescription): self
    {
        $this->fullDescription = $fullDescription;
        return $this;
    }
}
