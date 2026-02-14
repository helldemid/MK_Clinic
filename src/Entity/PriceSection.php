<?php

namespace App\Entity;

use App\Repository\PriceSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceSectionRepository::class)]
#[ORM\Table(name: 'price_section')]
#[ORM\UniqueConstraint(name: 'uniq_price_section_slug', columns: ['slug'])]
class PriceSection
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	#[ORM\Column(length: 255)]
	private string $title = '';

	#[ORM\Column(length: 255)]
	private string $navLabel = '';

	#[ORM\Column(length: 255)]
	private string $slug = '';

	#[ORM\Column(type: 'text', nullable: true)]
	private ?string $description = null;

	#[ORM\Column(type: 'text', nullable: true)]
	private ?string $note = null;

	#[ORM\Column]
	private int $position = 0;

	/**
	 * @var Collection<int, PriceColumn>
	 */
	#[ORM\OneToMany(mappedBy: 'section', targetEntity: PriceColumn::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
	#[ORM\OrderBy(['position' => 'ASC'])]
	private Collection $columns;

	/**
	 * @var Collection<int, PriceRow>
	 */
	#[ORM\OneToMany(mappedBy: 'section', targetEntity: PriceRow::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
	#[ORM\OrderBy(['position' => 'ASC'])]
	private Collection $rows;

	public function __construct()
	{
		$this->columns = new ArrayCollection();
		$this->rows = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
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

	public function getNavLabel(): string
	{
		return $this->navLabel;
	}

	public function setNavLabel(string $navLabel): self
	{
		$this->navLabel = $navLabel;

		return $this;
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

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): self
	{
		$this->description = $description;

		return $this;
	}

	public function getNote(): ?string
	{
		return $this->note;
	}

	public function setNote(?string $note): self
	{
		$this->note = $note;

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
	 * @return Collection<int, PriceColumn>
	 */
	public function getColumns(): Collection
	{
		return $this->columns;
	}

	public function addColumn(PriceColumn $column): self
	{
		if (!$this->columns->contains($column)) {
			$this->columns->add($column);
			$column->setSection($this);
		}

		return $this;
	}

	/**
	 * @return Collection<int, PriceRow>
	 */
	public function getRows(): Collection
	{
		return $this->rows;
	}

	public function addRow(PriceRow $row): self
	{
		if (!$this->rows->contains($row)) {
			$this->rows->add($row);
			$row->setSection($this);
		}

		return $this;
	}
}
