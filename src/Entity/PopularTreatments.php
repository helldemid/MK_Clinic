<?php

namespace App\Entity;

use App\Repository\PopularTreatmentsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PopularTreatmentsRepository::class)]
class PopularTreatments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Treatments::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Treatments $treatment = null;

    public function getId(): ?int
    {
        return $this->id;
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
