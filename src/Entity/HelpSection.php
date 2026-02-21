<?php

namespace App\Entity;

use App\Repository\HelpSectionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: HelpSectionRepository::class)]
#[ORM\Table(name: 'help_sections')]
class HelpSection
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column(type: 'integer')]
	private ?int $id = null;

	#[ORM\Column(type: 'string', length: 100, unique: true)]
	#[Assert\NotBlank]
	#[Assert\Length(max: 100)]
	private string $slug = '';

	#[ORM\Column(type: 'string', length: 255)]
	#[Assert\NotBlank]
	#[Assert\Length(max: 255)]
	private string $title = '';

	#[ORM\Column(type: 'text', columnDefinition: 'LONGTEXT')]
	private ?string $content = null;

	#[ORM\Column(type: 'integer')]
	#[Assert\PositiveOrZero]
	private int $position = 0;

	#[ORM\Column(type: 'boolean', options: ['default' => false])]
	private bool $faqSection = false;

	/**
	 * @var Collection<int, HelpFaq>
	 */
	#[ORM\OneToMany(mappedBy: 'section', targetEntity: HelpFaq::class, orphanRemoval: true, cascade: ['persist', 'remove'])]
	#[ORM\OrderBy(['position' => 'ASC', 'id' => 'ASC'])]
	private Collection $faqs;

	public function __construct()
	{
		$this->faqs = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function __toString(): string
	{
		return $this->title !== '' ? $this->title : sprintf('Help Section #%d', $this->id ?? 0);
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

	public function getContent(): ?string
	{
		return $this->content;
	}

	public function setContent(?string $content): self
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

	public function isFaqSection(): bool
	{
		return $this->faqSection;
	}

	public function setFaqSection(bool $faqSection): self
	{
		$this->faqSection = $faqSection;

		return $this;
	}

	/**
	 * @return Collection<int, HelpFaq>
	 */
	public function getFaqs(): Collection
	{
		return $this->faqs;
	}

	public function addFaq(HelpFaq $faq): self
	{
		if (!$this->faqs->contains($faq)) {
			$this->faqs->add($faq);
			$faq->setSection($this);
		}

		return $this;
	}

	public function removeFaq(HelpFaq $faq): self
	{
		if ($this->faqs->removeElement($faq) && $faq->getSection() === $this) {
			$faq->setSection(null);
		}

		return $this;
	}
}
