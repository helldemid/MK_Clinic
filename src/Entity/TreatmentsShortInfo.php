<?php

namespace App\Entity;

use App\Repository\TreatmentsShortInfoRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreatmentsShortInfoRepository::class)]
class TreatmentsShortInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(length: 255)]
    private string $description;

    #[ORM\ManyToOne(targetEntity: Treatments::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Treatments $treatment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function getDescription(): string
    {
        return $this->description;
    }

    public function getTreatment(): ?Treatments
    {
        return $this->treatment;
    }

    public function setTreatment(?Treatments $treatment): self
    {
        $this->treatment = $treatment;
        return $this;
    }
}
