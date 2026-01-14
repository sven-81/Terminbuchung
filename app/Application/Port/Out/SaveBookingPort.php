<?php

declare(strict_types=1);

namespace App\Application\Port\Out;

use App\Domain\Booking\Booking;
use App\Domain\Shared\ValueObject\BookingId;

interface SaveBookingPort
{
    public function save(Booking $booking): void;


    public function nextIdentity(): BookingId;
}






