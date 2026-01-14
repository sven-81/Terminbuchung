<?php

declare(strict_types=1);

namespace App\Domain\Booking;

use App\Domain\Booking\ValueObject\CustomerName;
use App\Domain\Booking\ValueObject\TimeSlot;
use App\Domain\Shared\ValueObject\BookingId;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use DateTimeImmutable;

final class Booking
{
    private function __construct(
        private BookingId $id,
        private ConsultantId $consultantId,
        private CustomerName $customerName,
        private Email $customerEmail,
        private TimeSlot $timeSlot,
        private DateTimeImmutable $createdAt
    ) {
    }

    public static function create(
        BookingId $id,
        ConsultantId $consultantId,
        CustomerName $customerName,
        Email $customerEmail,
        TimeSlot $timeSlot
    ): self {
        return new self(
            $id,
            $consultantId,
            $customerName,
            $customerEmail,
            $timeSlot,
            new DateTimeImmutable()
        );
    }

    public function id(): BookingId
    {
        return $this->id;
    }

    public function consultantId(): ConsultantId
    {
        return $this->consultantId;
    }

    public function customerName(): CustomerName
    {
        return $this->customerName;
    }

    public function customerEmail(): Email
    {
        return $this->customerEmail;
    }

    public function timeSlot(): TimeSlot
    {
        return $this->timeSlot;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}

