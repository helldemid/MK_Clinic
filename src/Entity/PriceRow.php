<?php

namespace App\Entity;

use App\Repository\PriceRowRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRowRepository::class)]
#[ORM\Table(name: 'price_row')]
#[ORM\UniqueConstraint(name: 'uniq_price_row_section_position', columns: ['section_id', 'position'])]
class PriceRow
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\ManyToOne(inversedBy: 'rows')]
	#[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
	private ?PriceSection $section = null;

	#[ORM\Column(length: 255)]
	private string $title = '';

	#[ORM\Column]
	private int $position = 0;

	/**
	 * @var Collection<int, PriceCell>
	 */
	#[ORM\OneToMany(mappedBy: 'row', targetEntity: PriceCell::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
	private Collection $cells;

	public function __construct()
	{
		$this->cells = new ArrayCollection();
	}

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

	public function getTitle(): string
	{
		return $this->title;
	}

	public function setTitle(string $title): self
	{
		$this->title = $title;

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

	/**
	 * @return Collection<int, PriceCell>
	 */
	public function getCells(): Collection
	{
		return $this->cells;
	}

	public function addCell(PriceCell $cell): self
	{
		if (!$this->cells->contains($cell)) {
			$this->cells->add($cell);
			$cell->setRow($this);
		}

		return $this;
	}

	public function removeCell(PriceCell $cell): self
	{
		if ($this->cells->removeElement($cell) && $cell->getRow() === $this) {
			$cell->setRow(null);
		}

		return $this;
	}
}
