<?php

namespace App\Entity;

use App\Repository\TreatmentPriceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreatmentPriceRepository::class)]
class TreatmentPrice
{

    public const PRICE_TYPE_SESSION = 'session';
    public const PRICE_TYPE_COURSE = 'course';
    public const PRICE_TYPE_DEFAULT = 'unset';

    public const PRICE_TYPES = [
        self::PRICE_TYPE_DEFAULT,
        self::PRICE_TYPE_SESSION,
        self::PRICE_TYPE_COURSE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private bool $isFixed = true;

    #[ORM\Column]
    private ?float $price;

    #[ORM\Column(length: 50)]
    private string $priceType = self::PRICE_TYPE_DEFAULT;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsFixed(): bool
    {
        return $this->isFixed;
    }

    public function setIsFixed(bool $isFixed): static
    {
        $this->isFixed = $isFixed;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceType(): string
    {
        return $this->priceType;
    }

    public function setPriceType(string $priceType): self
    {
        if (!in_array($priceType, self::PRICE_TYPES, true)) {
            throw new \InvalidArgumentException("Invalid price type: $priceType");
        }
        $this->priceType = $priceType;
        return $this;
    }
}
