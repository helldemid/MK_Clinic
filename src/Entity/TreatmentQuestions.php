<?php

namespace App\Entity;

use App\Repository\TreatmentQuestionsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreatmentQuestionsRepository::class)]
class TreatmentQuestions
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $question = null;

    #[ORM\Column(length: 10000)]
    private ?string $answer = null;

    #[ORM\ManyToOne(targetEntity: Treatments::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Treatments $treatment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): static
    {
        $this->answer = $answer;

        return $this;
    }

    public function getTreatment(): ?Treatments
    {
        return $this->treatment;
    }

    public function setTreatment(?Treatments $treatment): static
    {
        $this->treatment = $treatment;

        return $this;
    }
}
