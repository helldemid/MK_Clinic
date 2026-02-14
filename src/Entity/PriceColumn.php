<?php

namespace App\Entity;

use App\Repository\PriceColumnRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceColumnRepository::class)]
#[ORM\Table(name: 'price_column')]
#[ORM\UniqueConstraint(name: 'uniq_price_column_section_position', columns: ['section_id', 'position'])]
class PriceColumn
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'columns')]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?PriceSection $section = null;

	#[ORM\Column(length: 255)]
	private string $label = '';

	#[ORM\Column]
	private int $position = 0;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getSection(): ?PriceSection
	{
		return $this->section;
	}

	public function setSection(?PriceSection $section): self
	{
		$this->section = $section;

		return $this;
	}

	public function getLabel(): string
	{
		return $this->label;
	}

	public function setLabel(string $label): self
	{
		$this->label = $label;

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
