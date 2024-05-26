<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\Discount\EarlyBookingDiscountService;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(EarlyBookingDiscountService::class)]
class EarlyBookingDiscountServiceTest extends WebTestCase
{
    private EarlyBookingDiscountService $service;
    protected function setUp(): void
    {
        $service = static::getContainer()->get(EarlyBookingDiscountService::class);

        $now = new DateTimeImmutable('2024-01-01');
        $reflection = new \ReflectionClass($service);
        $property = $reflection->getProperty('now');
        $property->setValue($service, $now);

        $this->service = $service;
    }

    public function testWrongDate(): void
    {
        $this->expectException(Exception::class);
        $this->service->getDiscount(10000, payDate: new DateTimeImmutable('2024-01-02'));
    }

    /**
     * @return array<array<mixed>>
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     */
    public static function getCalcData(): array
    {
        return [
            [
                100,
                new DateTimeImmutable('2027-05-01'),
                new DateTimeImmutable('2026-05-01'),
                7
            ],
            [
                100,
                new DateTimeImmutable('2027-05-01'),
                new DateTimeImmutable('2026-12-31'),
                5
            ],
            [
                100,
                new DateTimeImmutable('2027-05-01'),
                new DateTimeImmutable('2027-01-31'),
                3
            ],
            [
                100,
                new DateTimeImmutable('2027-01-15'),
                new DateTimeImmutable('2026-08-31'),
                7
            ],
            [
                100,
                new DateTimeImmutable('2027-01-15'),
                new DateTimeImmutable('2026-09-30'),
                5
            ],
            [
                100,
                new DateTimeImmutable('2027-01-15'),
                new DateTimeImmutable('2026-10-31'),
                3
            ],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('getCalcData')]
    public function testCalculateDiscount(
        int $base,
        DateTimeImmutable $startDate,
        DateTimeImmutable $payDate,
        int $result
    ): void {
        $this->assertSame($result, $this->service->getDiscount($base, $startDate, $payDate));
    }
}
