<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Service;

use App\Application\Port\Out\LoadConsultantPort;
use App\Application\Port\Out\SaveBookingPort;
use App\Application\Service\CreateBookingService;
use App\Domain\Booking\Booking;
use App\Domain\Consultant\Consultant;
use App\Domain\Consultant\ValueObject\DailyCapacity;
use App\Domain\Shared\ValueObject\BookingId;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CreateBookingServiceTest extends TestCase
{
    private LoadConsultantPort $loadConsultantPort;

    private SaveBookingPort $saveBookingPort;

    private CreateBookingService $service;


    protected function setUp(): void
    {
        $this->loadConsultantPort = $this->createMock(LoadConsultantPort::class);
        $this->saveBookingPort = $this->createMock(SaveBookingPort::class);
        $this->service = new CreateBookingService($this->loadConsultantPort, $this->saveBookingPort);
    }


    public function testCreatesBookingSuccessfully(): void
    {
        $consultantId = ConsultantId::generate();
        $consultant = Consultant::create(
            $consultantId,
            'Dr. Anna Müller',
            Email::fromString('anna@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $bookingId = BookingId::generate();

        $this->loadConsultantPort
            ->expects($this->once())
            ->method('findById')
            ->with($this->callback(fn($id) => $id->equals($consultantId)))
            ->willReturn($consultant);

        $this->saveBookingPort
            ->expects($this->once())
            ->method('nextIdentity')
            ->willReturn($bookingId);

        $this->saveBookingPort
            ->expects($this->once())
            ->method('save')
            ->with(
                $this->callback(function (Booking $booking) use ($bookingId, $consultantId) {
                    return $booking->id()->equals($bookingId)
                        && $booking->consultantId()->equals($consultantId)
                        && $booking->customerName()->toString() === 'Max Mustermann'
                        && $booking->customerEmail()->toString() === 'max@example.com';
                })
            );

        $booking = $this->service->createBooking(
            $consultantId->toString(),
            'Max Mustermann',
            'max@example.com',
            new DateTimeImmutable('2026-01-20 10:00:00'),
            new DateTimeImmutable('2026-01-20 11:00:00')
        );

        $this->assertTrue($booking->id()->equals($bookingId));
        $this->assertTrue($booking->consultantId()->equals($consultantId));
        $this->assertSame('Max Mustermann', $booking->customerName()->toString());
    }


    public function testThrowsExceptionWhenConsultantNotFound(): void
    {
        $consultantId = ConsultantId::generate();

        $this->loadConsultantPort
            ->expects($this->once())
            ->method('findById')
            ->willReturn(null);

        $this->saveBookingPort
            ->expects($this->never())
            ->method('save');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Consultant not found');

        $this->service->createBooking(
            $consultantId->toString(),
            'Max Mustermann',
            'max@example.com',
            new DateTimeImmutable('2026-01-20 10:00:00'),
            new DateTimeImmutable('2026-01-20 11:00:00')
        );
    }


    public function testThrowsExceptionForInvalidEmail(): void
    {
        $consultantId = ConsultantId::generate();
        $consultant = Consultant::create(
            $consultantId,
            'Dr. Anna Müller',
            Email::fromString('anna@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $this->loadConsultantPort
            ->method('findById')
            ->willReturn($consultant);

        $this->saveBookingPort
            ->method('nextIdentity')
            ->willReturn(BookingId::generate());

        $this->saveBookingPort
            ->expects($this->never())
            ->method('save');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        $this->service->createBooking(
            $consultantId->toString(),
            'Max Mustermann',
            'invalid-email',
            new DateTimeImmutable('2026-01-20 10:00:00'),
            new DateTimeImmutable('2026-01-20 11:00:00')
        );
    }


    public function testThrowsExceptionForInvalidTimeSlot(): void
    {
        $consultantId = ConsultantId::generate();
        $consultant = Consultant::create(
            $consultantId,
            'Dr. Anna Müller',
            Email::fromString('anna@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $this->loadConsultantPort
            ->method('findById')
            ->willReturn($consultant);

        $this->saveBookingPort
            ->method('nextIdentity')
            ->willReturn(BookingId::generate());

        $this->saveBookingPort
            ->expects($this->never())
            ->method('save');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Start time must be before end time');

        $this->service->createBooking(
            $consultantId->toString(),
            'Max Mustermann',
            'max@example.com',
            new DateTimeImmutable('2026-01-20 11:00:00'),
            new DateTimeImmutable('2026-01-20 10:00:00')
        );
    }


    public function testThrowsExceptionForInvalidCustomerName(): void
    {
        $consultantId = ConsultantId::generate();
        $consultant = Consultant::create(
            $consultantId,
            'Dr. Anna Müller',
            Email::fromString('anna@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $this->loadConsultantPort
            ->method('findById')
            ->willReturn($consultant);

        $this->saveBookingPort
            ->method('nextIdentity')
            ->willReturn(BookingId::generate());

        $this->saveBookingPort
            ->expects($this->never())
            ->method('save');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Customer name cannot be empty');

        $this->service->createBooking(
            $consultantId->toString(),
            '   ',
            'max@example.com',
            new DateTimeImmutable('2026-01-20 10:00:00'),
            new DateTimeImmutable('2026-01-20 11:00:00')
        );
    }
}

