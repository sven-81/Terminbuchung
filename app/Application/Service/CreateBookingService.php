<?php

declare(strict_types=1);

namespace App\Application\Service;

use App\Application\Port\Out\LoadConsultantPort;
use App\Application\Port\Out\SaveBookingPort;
use App\Domain\Booking\Booking;
use App\Domain\Booking\ValueObject\CustomerName;
use App\Domain\Booking\ValueObject\TimeSlot;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class CreateBookingService
{
    public function __construct(
        private LoadConsultantPort $loadConsultantPort,
        private SaveBookingPort $saveBookingPort
    ) {
    }

    public function createBooking(
        string $consultantId,
        string $customerName,
        string $customerEmail,
        DateTimeImmutable $startsAt,
        DateTimeImmutable $endsAt
    ): Booking {
        $consultantIdVO = ConsultantId::fromString($consultantId);

        $consultant = $this->loadConsultantPort->findById($consultantIdVO);
        if ($consultant === null) {
            throw new InvalidArgumentException('Consultant not found');
        }

        $booking = Booking::create(
            $this->saveBookingPort->nextIdentity(),
            $consultantIdVO,
            CustomerName::fromString($customerName),
            Email::fromString($customerEmail),
            TimeSlot::fromDateTimes($startsAt, $endsAt)
        );

        $this->saveBookingPort->save($booking);

        return $booking;
    }
}

