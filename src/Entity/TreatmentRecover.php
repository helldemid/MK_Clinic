<?php

namespace App\Entity;

use App\Repository\TreatmentRecoverRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TreatmentRecoverRepository::class)]
class TreatmentRecover
{

    public const RECOVER_PERIOD_TYPE_MINUTES = 'minutes';
    public const RECOVER_PERIOD_TYPE_HOURS = 'hours';
    public const RECOVER_PERIOD_TYPE_DAYS = 'days';
    public const RECOVER_PERIOD_TYPE_WEEKS = 'weeks';
    public const RECOVER_PERIOD_TYPE_MONTHS = 'months';
    public const RECOVER_PERIOD_TYPE_NONE = 'Minimal recover time';

    public const RECOVER_PERIODS = [
        self::RECOVER_PERIOD_TYPE_MINUTES,
        self::RECOVER_PERIOD_TYPE_HOURS,
        self::RECOVER_PERIOD_TYPE_DAYS,
        self::RECOVER_PERIOD_TYPE_WEEKS,
        self::RECOVER_PERIOD_TYPE_MONTHS,
        self::RECOVER_PERIOD_TYPE_NONE,
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $min = null;

    #[ORM\Column]
    private ?int $max = null;

    #[ORM\Column(type: 'string', length: 50, nullable: false, options: ['default' => self::RECOVER_PERIOD_TYPE_NONE])]
    private string $period = self::RECOVER_PERIOD_TYPE_NONE;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMin(): ?int
    {
        return $this->min;
    }

    public function setMin(int $min): static
    {
        $this->min = $min;

        return $this;
    }

    public function getMax(): ?int
    {
        return $this->max;
    }

    public function setMax(int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function getPeriod(): string
    {
        return $this->period;
    }

    public function setPeriod(string $period): self
    {
        if (!in_array($period, self::RECOVER_PERIODS, true)) {
            throw new \InvalidArgumentException("Invalid recover period: $period");
        }
        $this->period = $period;
        return $this;
    }
}
