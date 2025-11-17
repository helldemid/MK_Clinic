<?php

namespace App\Entity;

use App\Repository\HelpSectionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HelpSectionRepository::class)]
#[ORM\Table(name: 'help_sections')]
class HelpSection
{
	// --- ID ---------------------------------------------------

	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	// --- SLUG (уникальный системный ключ) -----------------------

	#[ORM\Column(type: 'string', length: 100, unique: true)]
	#[Assert\NotBlank]
	#[Assert\Length(max: 100)]
	private string $slug;

	// --- Title (название пункта меню) ---------------------------

	#[ORM\Column(type: 'string', length: 255)]
	#[Assert\NotBlank]
	#[Assert\Length(max: 255)]
	private string $title;

	// --- Content (HTML текст раздела) ---------------------------

	#[ORM\Column(type: 'text')]
	#[Assert\NotBlank]
	private string $content;

	// --- Position (сортировка в меню) ---------------------------

	#[ORM\Column(type: 'integer')]
	#[Assert\PositiveOrZero]
	private int $position = 0;

	// =============================================================
	// Getters / Setters
	// =============================================================

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getSlug(): string
	{
		return $this->slug;
	}

	public function setSlug(string $slug): self
	{
		$this->slug = $slug;
		return $this;
	}

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	public function getContent(): string
	{
		return $this->content;
	}

	public function setContent(string $content): self
	{
		$this->content = $content;
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
