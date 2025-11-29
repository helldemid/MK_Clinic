<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private ?string $name = null;

	#[ORM\Column(type: 'integer', options: ['default' => 0])]
	private int $pabauMasterCategoryId = 0;

	#[ORM\Column(type: 'boolean', options: ['default' => 1])]
	private bool $is_shown = true;

	public function getPabauMasterCategoryId(): int { return $this->pabauMasterCategoryId; }

	public function setPabauMasterCategoryId(int $pabauMasterCategoryId): static
	{
		$this->pabauMasterCategoryId = $pabauMasterCategoryId;
		return $this;
	}

	public function isShown(): bool
	{
		return $this->is_shown;
	}

	public function setIsShown(bool $is_shown): static
	{
		$this->is_shown = $is_shown;
		return $this;
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): static
	{
		$this->name = $name;

		return $this;
	}
}
