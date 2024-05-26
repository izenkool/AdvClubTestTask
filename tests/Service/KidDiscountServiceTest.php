<?php

namespace App\Tests\Service;

use App\Service\Discount\KidDiscountService;
use DateTimeImmutable;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use ReflectionException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[CoversClass(KidDiscountService::class)]
class KidDiscountServiceTest extends KernelTestCase
{
    private KidDiscountService $service;

    /**
     * @throws ReflectionException
     */
    protected function setUp(): void
    {
        $service = static::getContainer()->get(KidDiscountService::class);

        $now = new DateTimeImmutable('2023-01-01');
        $reflection = new ReflectionClass($service);
        $property = $reflection->getProperty('now');
        $property->setValue($service, $now);

        $this->service = $service;
    }

    public function testWrongDateDiscount(): void
    {
        $this->expectException(Exception::class);
        $this->service->getDiscount(1000, new DateTimeImmutable('2024-01-01'));
    }


    /**
     * @return array<array<mixed>>
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     */
    public static function getCalcData(): array
    {
        return [
            [10000, new DateTimeImmutable('2019-01-01'), 8000],
            [10000, new DateTimeImmutable('2015-10-18'), 3000],
            [100000, new DateTimeImmutable('2015-10-18'), 4500],
            [100000, new DateTimeImmutable('2010-10-18'), 10000],
            [100000, new DateTimeImmutable('2022-10-18'), 0],
            [100000, new DateTimeImmutable('2003-10-18'), 0],
        ];
    }

    /**
     * @throws Exception
     */
    #[DataProvider('getCalcData')]
    public function testCalculateDiscount(int $base, DateTimeImmutable $birthDate, int $result): void
    {
        $this->assertSame($result, $this->service->getDiscount($base, $birthDate));
    }
}