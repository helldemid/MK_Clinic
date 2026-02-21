<?php

namespace App\Entity;

use App\Repository\SiteContentSettingsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteContentSettingsRepository::class)]
#[ORM\Table(name: 'site_content_settings')]
class SiteContentSettings
{
	#[ORM\Id]
	#[ORM\GeneratedValue]
	#[ORM\Column]
	private ?int $id = null;

	/**
	 * @var array<int, array{text: string, url: string}>
	 */
	#[ORM\Column(type: 'json', nullable: true)]
	private ?array $promoItems = [];

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $heroDesktopImage = null;

	#[ORM\Column(length: 255, nullable: true)]
	private ?string $heroMobileImage = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	/**
	 * @return array<int, array{text: string, url: string}>
	 */
	public function getPromoItems(): array
	{
		$items = is_array($this->promoItems) ? $this->promoItems : [];

		return array_values(array_filter(array_map(static function ($item): ?array {
			if (!is_array($item)) {
				return null;
			}

			$text = trim((string) ($item['text'] ?? ''));
			$url = trim((string) ($item['url'] ?? ''));
			if ($text == '') {
				return null;
			}

			return [
				'text' => $text,
				'url' => $url,
			];
		}, $items)));
	}

	/**
	 * @param array<int, array<string, mixed>>|null $promoItems
	 */
	public function setPromoItems(?array $promoItems): self
	{
		$this->promoItems = $promoItems ?? [];

		return $this;
	}

	public function getHeroDesktopImage(): ?string
	{
		return $this->heroDesktopImage;
	}

	public function setHeroDesktopImage(?string $heroDesktopImage): self
	{
		$this->heroDesktopImage = $heroDesktopImage;

		return $this;
	}

	public function getHeroMobileImage(): ?string
	{
		return $this->heroMobileImage;
	}

	public function setHeroMobileImage(?string $heroMobileImage): self
	{
		$this->heroMobileImage = $heroMobileImage;

		return $this;
	}
}
