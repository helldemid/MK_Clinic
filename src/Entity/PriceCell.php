<?php

namespace App\Entity;

use App\Repository\PriceCellRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceCellRepository::class)]
#[ORM\Table(name: 'price_cell')]
#[ORM\UniqueConstraint(name: 'uniq_price_cell_row_column', columns: ['row_id', 'column_id'])]
class PriceCell
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'cells')]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?PriceRow $row = null;

	#[ORM\ManyToOne]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?PriceColumn $column = null;

	#[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
	private ?string $value = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getRow(): ?PriceRow
	{
		return $this->row;
	}

	public function setRow(?PriceRow $row): self
	{
		$this->row = $row;

		return $this;
	}

	public function getColumn(): ?PriceColumn
	{
		return $this->column;
	}

	public function setColumn(?PriceColumn $column): self
	{
		$this->column = $column;

		return $this;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function setValue(?string $value): self
	{
		$this->value = $value;

		return $this;
	}
}
