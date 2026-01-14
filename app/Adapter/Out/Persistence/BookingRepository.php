<?php

declare(strict_types=1);

namespace App\Adapter\Out\Persistence;

use App\Application\Port\Out\SaveBookingPort;
use App\Domain\Booking\Booking;
use App\Domain\Shared\ValueObject\BookingId;
use Illuminate\Support\Facades\DB;

final readonly class BookingRepository implements SaveBookingPort
{
    public function save(Booking $booking): void
    {
        DB::table('bookings')->insert([
            'id' => $booking->id()->toString(),
            'consultant_id' => $booking->consultantId()->toString(),
            'customer_name' => $booking->customerName()->toString(),
            'customer_email' => $booking->customerEmail()->toString(),
            'starts_at' => $booking->timeSlot()->startsAt(),
            'ends_at' => $booking->timeSlot()->endsAt(),
            'created_at' => $booking->createdAt(),
            'updated_at' => $booking->createdAt(),
        ]);
    }

    public function nextIdentity(): BookingId
    {
        return BookingId::generate();
    }
}

