<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Service\CreateBookingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use DateTimeImmutable;
use InvalidArgumentException;

final class BookingController
{
    public function __construct(
        private readonly CreateBookingService $createBookingService
    ) {
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'consultant_id' => 'required|string|uuid',
            'customer_name' => 'required|string|min:1|max:255',
            'customer_email' => 'required|email',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date',
        ]);

        try {
            $booking = $this->createBookingService->createBooking(
                $validated['consultant_id'],
                $validated['customer_name'],
                $validated['customer_email'],
                new DateTimeImmutable($validated['starts_at']),
                new DateTimeImmutable($validated['ends_at'])
            );

            return response()->json([
                'data' => [
                    'id' => $booking->id()->toString(),
                    'consultant_id' => $booking->consultantId()->toString(),
                    'customer_name' => $booking->customerName()->toString(),
                    'customer_email' => $booking->customerEmail()->toString(),
                    'starts_at' => $booking->timeSlot()->startsAt()->format('Y-m-d\TH:i:s\Z'),
                    'ends_at' => $booking->timeSlot()->endsAt()->format('Y-m-d\TH:i:s\Z'),
                    'duration_minutes' => $booking->timeSlot()->durationInMinutes(),
                    'created_at' => $booking->createdAt()->format('Y-m-d\TH:i:s\Z'),
                ],
            ], 201);
        } catch (InvalidArgumentException $e) {
            return response()->json([
                'message' => 'Booking cannot be created due to business rule violations',
                'errors' => [
                    'booking' => [$e->getMessage()],
                ],
            ], 409);
        }
    }
}

