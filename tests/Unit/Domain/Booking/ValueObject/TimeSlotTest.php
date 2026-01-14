<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking\ValueObject;

use App\Domain\Booking\ValueObject\TimeSlot;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TimeSlotTest extends TestCase
{
    public function testCreatesValidTimeSlot(): void
    {
        $start = new DateTimeImmutable('2026-01-20 10:00:00');
        $end = new DateTimeImmutable('2026-01-20 11:00:00');

        $timeSlot = TimeSlot::fromDateTimes($start, $end);

        $this->assertEquals($start, $timeSlot->startsAt());
        $this->assertEquals($end, $timeSlot->endsAt());
    }


    public function testThrowsExceptionWhenStartIsAfterEnd(): void
    {
        $start = new DateTimeImmutable('2026-01-20 11:00:00');
        $end = new DateTimeImmutable('2026-01-20 10:00:00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Start time must be before end time');

        TimeSlot::fromDateTimes($start, $end);
    }


    public function testThrowsExceptionWhenStartEqualsEnd(): void
    {
        $time = new DateTimeImmutable('2026-01-20 10:00:00');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Start time must be before end time');

        TimeSlot::fromDateTimes($time, $time);
    }


    /**
     * @dataProvider durationProvider
     */
    public function testCalculatesDurationInMinutes(string $start, string $end, int $expectedMinutes): void
    {
        $timeSlot = TimeSlot::fromDateTimes(
            new DateTimeImmutable($start),
            new DateTimeImmutable($end)
        );

        $this->assertSame($expectedMinutes, $timeSlot->durationInMinutes());
    }


    public static function durationProvider(): array
    {
        return [
            'one hour' => ['2026-01-20 10:00:00', '2026-01-20 11:00:00', 60],
            'half hour' => ['2026-01-20 10:00:00', '2026-01-20 10:30:00', 30],
            'two hours' => ['2026-01-20 10:00:00', '2026-01-20 12:00:00', 120],
            '15 minutes' => ['2026-01-20 10:00:00', '2026-01-20 10:15:00', 15],
            '90 minutes' => ['2026-01-20 10:00:00', '2026-01-20 11:30:00', 90],
        ];
    }


    /**
     * @dataProvider sameDateProvider
     */
    public function testIsOnDateReturnsTrueForSameDate(string $slotStart, string $slotEnd, string $checkDate): void
    {
        $timeSlot = TimeSlot::fromDateTimes(
            new DateTimeImmutable($slotStart),
            new DateTimeImmutable($slotEnd)
        );

        $this->assertTrue($timeSlot->isOnDate(new DateTimeImmutable($checkDate)));
    }


    public static function sameDateProvider(): array
    {
        return [
            'same day morning' => ['2026-01-20 10:00:00', '2026-01-20 11:00:00', '2026-01-20 00:00:00'],
            'same day evening' => ['2026-01-20 10:00:00', '2026-01-20 11:00:00', '2026-01-20 23:59:59'],
            'same day noon' => ['2026-01-20 10:00:00', '2026-01-20 11:00:00', '2026-01-20 12:00:00'],
        ];
    }


    public function testIsOnDateReturnsFalseForDifferentDate(): void
    {
        $timeSlot = TimeSlot::fromDateTimes(
            new DateTimeImmutable('2026-01-20 10:00:00'),
            new DateTimeImmutable('2026-01-20 11:00:00')
        );

        $this->assertFalse($timeSlot->isOnDate(new DateTimeImmutable('2026-01-21 10:00:00')));
    }


    /**
     * @dataProvider overlapProvider
     */
    public function testDetectsOverlappingTimeSlots(
        string $slot1Start,
        string $slot1End,
        string $slot2Start,
        string $slot2End,
        bool   $shouldOverlap
    ): void {
        $slot1 = TimeSlot::fromDateTimes(
            new DateTimeImmutable($slot1Start),
            new DateTimeImmutable($slot1End)
        );
        $slot2 = TimeSlot::fromDateTimes(
            new DateTimeImmutable($slot2Start),
            new DateTimeImmutable($slot2End)
        );

        $this->assertSame($shouldOverlap, $slot1->overlapsWith($slot2));
    }


    public static function overlapProvider(): array
    {
        return [
            'partial overlap' => [
                '2026-01-20 10:00:00',
                '2026-01-20 11:00:00',
                '2026-01-20 10:30:00',
                '2026-01-20 11:30:00',
                true,
            ],
            'complete overlap' => [
                '2026-01-20 10:00:00',
                '2026-01-20 12:00:00',
                '2026-01-20 10:30:00',
                '2026-01-20 11:30:00',
                true,
            ],
            'exact same time' => [
                '2026-01-20 10:00:00',
                '2026-01-20 11:00:00',
                '2026-01-20 10:00:00',
                '2026-01-20 11:00:00',
                true,
            ],
            'no overlap - before' => [
                '2026-01-20 10:00:00',
                '2026-01-20 11:00:00',
                '2026-01-20 11:00:00',
                '2026-01-20 12:00:00',
                false,
            ],
            'no overlap - after' => [
                '2026-01-20 11:00:00',
                '2026-01-20 12:00:00',
                '2026-01-20 10:00:00',
                '2026-01-20 11:00:00',
                false,
            ],
            'one minute overlap' => [
                '2026-01-20 10:00:00',
                '2026-01-20 11:00:00',
                '2026-01-20 10:59:00',
                '2026-01-20 12:00:00',
                true,
            ],
        ];
    }


    public function testIsInPastReturnsTrueForPastTimeSlot(): void
    {
        $timeSlot = TimeSlot::fromDateTimes(
            new DateTimeImmutable('2020-01-01 10:00:00'),
            new DateTimeImmutable('2020-01-01 11:00:00')
        );

        $this->assertTrue($timeSlot->isInPast());
    }


    public function testIsInPastReturnsFalseForFutureTimeSlot(): void
    {
        $timeSlot = TimeSlot::fromDateTimes(
            new DateTimeImmutable('2030-01-01 10:00:00'),
            new DateTimeImmutable('2030-01-01 11:00:00')
        );

        $this->assertFalse($timeSlot->isInPast());
    }
}

