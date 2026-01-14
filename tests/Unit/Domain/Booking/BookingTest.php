<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking;

use App\Domain\Booking\Booking;
use App\Domain\Booking\ValueObject\CustomerName;
use App\Domain\Booking\ValueObject\TimeSlot;
use App\Domain\Shared\ValueObject\BookingId;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class BookingTest extends TestCase
{
    public function testCreatesBooking(): void
    {
        $id = BookingId::generate();
        $consultantId = ConsultantId::generate();
        $customerName = CustomerName::fromString('Max Mustermann');
        $customerEmail = Email::fromString('max@example.com');
        $timeSlot = TimeSlot::fromDateTimes(
            new DateTimeImmutable('2026-01-20 10:00:00'),
            new DateTimeImmutable('2026-01-20 11:00:00')
        );

        $booking = Booking::create($id, $consultantId, $customerName, $customerEmail, $timeSlot);

        $this->assertTrue($booking->id()->equals($id));
        $this->assertTrue($booking->consultantId()->equals($consultantId));
        $this->assertSame('Max Mustermann', $booking->customerName()->toString());
        $this->assertTrue($booking->customerEmail()->equals($customerEmail));
        $this->assertEquals($timeSlot, $booking->timeSlot());
        $this->assertInstanceOf(DateTimeImmutable::class, $booking->createdAt());
    }

    public function testCreatedAtIsSetAutomatically(): void
    {
        $before = new DateTimeImmutable();

        $booking = Booking::create(
            BookingId::generate(),
            ConsultantId::generate(),
            CustomerName::fromString('Test'),
            Email::fromString('test@example.com'),
            TimeSlot::fromDateTimes(
                new DateTimeImmutable('2026-01-20 10:00:00'),
                new DateTimeImmutable('2026-01-20 11:00:00')
            )
        );

        $after = new DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $booking->createdAt());
        $this->assertLessThanOrEqual($after, $booking->createdAt());
    }

    public function testDifferentBookingsHaveDifferentIds(): void
    {
        $consultantId = ConsultantId::generate();

        $booking1 = Booking::create(
            BookingId::generate(),
            $consultantId,
            CustomerName::fromString('Customer 1'),
            Email::fromString('c1@example.com'),
            TimeSlot::fromDateTimes(
                new DateTimeImmutable('2026-01-20 10:00:00'),
                new DateTimeImmutable('2026-01-20 11:00:00')
            )
        );

        $booking2 = Booking::create(
            BookingId::generate(),
            $consultantId,
            CustomerName::fromString('Customer 2'),
            Email::fromString('c2@example.com'),
            TimeSlot::fromDateTimes(
                new DateTimeImmutable('2026-01-20 14:00:00'),
                new DateTimeImmutable('2026-01-20 15:00:00')
            )
        );

        $this->assertFalse($booking1->id()->equals($booking2->id()));
    }

    public function testCanCreateBookingForSameConsultantAtDifferentTimes(): void
    {
        $consultantId = ConsultantId::generate();

        $booking1 = Booking::create(
            BookingId::generate(),
            $consultantId,
            CustomerName::fromString('Customer 1'),
            Email::fromString('c1@example.com'),
            TimeSlot::fromDateTimes(
                new DateTimeImmutable('2026-01-20 10:00:00'),
                new DateTimeImmutable('2026-01-20 11:00:00')
            )
        );

        $booking2 = Booking::create(
            BookingId::generate(),
            $consultantId,
            CustomerName::fromString('Customer 2'),
            Email::fromString('c2@example.com'),
            TimeSlot::fromDateTimes(
                new DateTimeImmutable('2026-01-20 11:00:00'),
                new DateTimeImmutable('2026-01-20 12:00:00')
            )
        );

        $this->assertTrue($booking1->consultantId()->equals($booking2->consultantId()));
        $this->assertFalse($booking1->id()->equals($booking2->id()));
    }
}

