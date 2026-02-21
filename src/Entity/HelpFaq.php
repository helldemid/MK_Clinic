<?php

namespace App\Entity;

use App\Repository\HelpFaqRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HelpFaqRepository::class)]
#[ORM\Table(name: 'help_faq')]
#[ORM\UniqueConstraint(name: 'uniq_help_faq_section_position', columns: ['section_id', 'position'])]
class HelpFaq
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'faqs')]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?HelpSection $section = null;

	#[ORM\Column(length: 255)]
	#[Assert\NotBlank]
	#[Assert\Length(max: 255)]
	private string $question = '';

	#[ORM\Column(type: 'text', nullable: true)]
	private ?string $answer = null;

	#[ORM\Column]
	#[Assert\PositiveOrZero]
	private int $position = 0;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function __toString(): string
	{
		return $this->question !== '' ? $this->question : sprintf('FAQ #%d', $this->id ?? 0);
	}

	public function getSection(): ?HelpSection
	{
		return $this->section;
	}

	public function setSection(?HelpSection $section): self
	{
		$this->section = $section;

		return $this;
	}

	public function getQuestion(): string
	{
		return $this->question;
	}

	public function setQuestion(string $question): self
	{
		$this->question = $question;

		return $this;
	}

	public function getAnswer(): ?string
	{
		return $this->answer;
	}

	public function setAnswer(?string $answer): self
	{
		$this->answer = $answer;

		return $this;
	}

	public function getPosition(): int
	{
		return $this->position;
	}

	public function setPosition(int $position): self
	{
		$this->position = $position;

		return $this;
	}
}
