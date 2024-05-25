<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class TravelCostDTO
{
    public function __construct(
        #[Assert\NotBlank]
        private int $cost,
        #[Assert\NotBlank]
        #[Assert\Date]
        private string $birthDate,
        #[Assert\Date]
        private ?string $startDate = null,
        #[Assert\Date]
        private ?string $payDate = null
    )
    {}

    public function getCost(): int
    {
        return $this->cost;
    }

    public function getBirthDate(): string
    {
        return $this->birthDate;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getPayDate(): ?string
    {
        return $this->payDate;
    }
}