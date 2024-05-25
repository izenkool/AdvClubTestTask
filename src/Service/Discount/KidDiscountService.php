<?php

namespace App\Service\Discount;

class KidDiscountService
{
    private \DateTimeImmutable $now;
    public function __construct()
    {
        $this->now = new \DateTimeImmutable();
    }

    private const DISCOUNT_MAP = [
        [
            'min' => 3,
            'max' => 6,
            'percent' => 80,
            'max_sum' => null
        ],
        [
            'min' => 6,
            'max' => 12,
            'percent' => 30,
            'max_sum' => 4500,
        ],
        [
            'min' => 12,
            'max' => 18,
            'percent' => 10,
            'max_sum' => null,
        ],
    ];

    /**
     * @throws \Exception
     */
    public function getDiscount(int $sum, \DateTimeImmutable $birthDate): int
    {
        if ($birthDate > $this->now) {
            throw new \Exception('wrong birth date');
        }
        $age = $birthDate->diff($this->now)->y;

        $discount = 0;
        foreach (self::DISCOUNT_MAP as $discountItem) {
            if ($age >= $discountItem['max'] || $age < $discountItem['min']) {
                continue;
            }

            $discount = $discountItem['percent'] / 100 * $sum;
            if (null !== $discountItem['max_sum'] && $discount > $discountItem['max_sum']) {
                $discount = $discountItem['max_sum'];
            }
            break;
        }

        return floor($discount);
    }
}