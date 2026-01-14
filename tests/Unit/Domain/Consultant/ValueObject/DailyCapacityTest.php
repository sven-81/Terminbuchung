<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Consultant\ValueObject;

use App\Domain\Consultant\ValueObject\DailyCapacity;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class DailyCapacityTest extends TestCase
{
    /**
     * @dataProvider validCapacityProvider
     */
    public function testCreatesFromValidMinutes(int $minutes): void
    {
        $capacity = DailyCapacity::fromMinutes($minutes);

        $this->assertSame($minutes, $capacity->minutes());
    }


    public static function validCapacityProvider(): array
    {
        return [
            'one hour' => [60],
            'four hours' => [240],
            'eight hours' => [480],
            'twelve hours' => [720],
            'full day' => [1440],
        ];
    }


    public function testCreatesEightHoursCapacity(): void
    {
        $capacity = DailyCapacity::eightHours();

        $this->assertSame(480, $capacity->minutes());
    }


    /**
     * @dataProvider invalidCapacityProvider
     */
    public function testThrowsExceptionForInvalidCapacity(int $minutes, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        DailyCapacity::fromMinutes($minutes);
    }


    public static function invalidCapacityProvider(): array
    {
        return [
            'zero' => [0, 'Daily capacity must be positive'],
            'negative' => [-100, 'Daily capacity must be positive'],
            'too large' => [1441, 'Daily capacity cannot exceed 24 hours'],
            'way too large' => [5000, 'Daily capacity cannot exceed 24 hours'],
        ];
    }


    /**
     * @dataProvider capacityExceededProvider
     */
    public function testIsExceededBy(int $capacity, int $usedMinutes, bool $shouldBeExceeded): void
    {
        $dailyCapacity = DailyCapacity::fromMinutes($capacity);

        $this->assertSame($shouldBeExceeded, $dailyCapacity->isExceededBy($usedMinutes));
    }


    public static function capacityExceededProvider(): array
    {
        return [
            'not exceeded' => [480, 300, false],
            'exactly at capacity' => [480, 480, false],
            'exceeded by one' => [480, 481, true],
            'heavily exceeded' => [480, 600, true],
            'zero used' => [480, 0, false],
        ];
    }


    /**
     * @dataProvider remainingMinutesProvider
     */
    public function testCalculatesRemainingMinutes(int $capacity, int $usedMinutes, int $expectedRemaining): void
    {
        $dailyCapacity = DailyCapacity::fromMinutes($capacity);

        $this->assertSame($expectedRemaining, $dailyCapacity->remainingMinutes($usedMinutes));
    }


    public static function remainingMinutesProvider(): array
    {
        return [
            'half used' => [480, 240, 240],
            'nothing used' => [480, 0, 480],
            'fully used' => [480, 480, 0],
            'exceeded returns zero' => [480, 500, 0],
            'almost full' => [480, 470, 10],
        ];
    }


    public function testRemainingMinutesNeverReturnsNegative(): void
    {
        $capacity = DailyCapacity::fromMinutes(480);

        $remaining = $capacity->remainingMinutes(1000);

        $this->assertSame(0, $remaining);
    }
}

