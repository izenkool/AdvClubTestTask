<?php

namespace App\Service\Discount;

class EarlyBookingDiscountService
{
    private \DateTimeImmutable $now;

    public function __construct()
    {
        $this->now = new \DateTimeImmutable();
    }

    private const MAX_DISCOUNT = 1500;

    public function getDiscount(
        int $sum,
        ?\DateTimeImmutable $startDate = null,
        ?\DateTimeImmutable $payDate = null
    ): int {
        if (null === $payDate) {
            return 0;
        }

        $startDate ??= $this->now;
        if ($payDate > $startDate) {
            throw new \Exception('pay date must be later than start date');
        }

        $discount = (int) ($sum * ($this->getPercent($startDate, $payDate) / 100));

        return min($discount, self::MAX_DISCOUNT);
    }

    private function getPercent(\DateTimeImmutable $startDate, \DateTimeImmutable $payDate): int
    {
        $startYear = (int) $startDate->format('Y');
        $payYear = (int) $payDate->format('Y');
        $payMonth = (int) $payDate->format('m');

        // Старт 1 апреля - 30 сентября следующего года
        $april1NextYear = new \DateTime($startYear.'-04-01');
        $september30NextYear = new \DateTime($startYear.'-09-30');
        if ($startDate >= $april1NextYear && $startDate <= $september30NextYear) {
            if ($payMonth <= 11 && $payYear <= $startYear - 1) {
                return 7;
            } elseif (12 == $payMonth && $payYear == $startYear - 1) {
                return 5;
            } elseif (1 == $payMonth && $payYear == $startYear) {
                return 3;
            }
        }

        // Старт 1 октября текущего года - 14 января следующего года
        $october1CurrentYear = new \DateTime($startYear.'-10-01');
        $january14NextYear = new \DateTime($startYear.'-01-14');
        if ($startDate >= $october1CurrentYear && $startDate <= $january14NextYear) {
            if ($payMonth <= 3 && $payYear <= $startYear) {
                return 7;
            } elseif (4 == $payMonth && $payYear == $startYear) {
                return 5;
            } elseif (5 == $payMonth && $payYear == $startYear) {
                return 3;
            }
        }

        // Старт 15 января следующего года и далее
        $january15NextYear = new \DateTime($startYear.'-01-15');
        if ($startDate >= $january15NextYear) {
            if ($payMonth <= 8 && $payYear <= $startYear - 1) {
                return 7;
            } elseif (9 == $payMonth && $payYear == $startYear - 1) {
                return 5;
            } elseif (10 == $payMonth && $payYear == $startYear - 1) {
                return 3;
            }
        }

        return 0;
    }
}
