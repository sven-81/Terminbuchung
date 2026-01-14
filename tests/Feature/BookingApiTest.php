<?php

declare(strict_types=1);

namespace Tests\Feature;

use Database\Seeders\ConsultantSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BookingApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ConsultantSeeder::class);
    }

    public function testListsConsultants(): void
    {
        $response = $this->getJson('/api/consultants');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'daily_capacity_minutes',
                        'created_at',
                    ],
                ],
            ]);

        $this->assertCount(2, $response->json('data'), 'Expected 2 consultants from seeder');
    }

    public function testCreatesBookingSuccessfully(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Max Mustermann',
            'customer_email' => 'max.mustermann@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'consultant_id',
                    'customer_name',
                    'customer_email',
                    'starts_at',
                    'ends_at',
                    'duration_minutes',
                    'created_at',
                ],
            ])
            ->assertJson([
                'data' => [
                    'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
                    'customer_name' => 'Max Mustermann',
                    'customer_email' => 'max.mustermann@example.com',
                    'duration_minutes' => 60,
                ],
            ]);

        $this->assertDatabaseHas('bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Max Mustermann',
            'customer_email' => 'max.mustermann@example.com',
        ]);
    }

    public function testReturnsValidationErrorForMissingFields(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Test User',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'customer_email',
                    'starts_at',
                    'ends_at',
                ],
            ]);
    }

    public function testReturnsValidationErrorForInvalidEmail(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Test User',
            'customer_email' => 'invalid-email',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'customer_email',
                ],
            ]);
    }

    public function testReturnsValidationErrorForInvalidUuid(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => 'not-a-uuid',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'consultant_id',
                ],
            ]);
    }

    public function testReturnsValidationErrorForInvalidDate(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'starts_at' => 'not-a-date',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'starts_at',
                ],
            ]);
    }

    public function testReturnsConflictForNonExistentConsultant(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '00000000-0000-0000-0000-000000000000',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(409)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'booking',
                ],
            ])
            ->assertJson([
                'message' => 'Booking cannot be created due to business rule violations',
                'errors' => [
                    'booking' => ['Consultant not found'],
                ],
            ]);
    }

    public function testReturnsConflictForInvalidTimeSlot(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'starts_at' => '2026-01-20T11:00:00Z',
            'ends_at' => '2026-01-20T10:00:00Z',
        ]);

        $response->assertStatus(409)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'booking',
                ],
            ])
            ->assertJson([
                'errors' => [
                    'booking' => ['Start time must be before end time'],
                ],
            ]);
    }

    public function testReturnsValidationErrorForEmptyCustomerName(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => '   ',
            'customer_email' => 'test@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        // Laravel's 'required' validation catches empty/whitespace-only strings before domain logic
        $response->assertStatus(422)
            ->assertJsonStructure([
                'message',
                'errors' => [
                    'customer_name',
                ],
            ]);
    }

    public function testAcceptsLongCustomerName(): void
    {
        $longName = 'Dr. Maximilian Alexander Ferdinand von Mustermann-Schmidt';

        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => $longName,
            'customer_email' => 'dr.mustermann@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'customer_name' => $longName,
                ],
            ]);
    }

    public function testCreatesMultipleBookingsForSameConsultant(): void
    {
        $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Customer 1',
            'customer_email' => 'customer1@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ])->assertStatus(201);

        $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Customer 2',
            'customer_email' => 'customer2@example.com',
            'starts_at' => '2026-01-20T14:00:00Z',
            'ends_at' => '2026-01-20T15:00:00Z',
        ])->assertStatus(201);

        $this->assertDatabaseCount('bookings', 2);
    }

    public function testReturnsCorrectDurationInMinutes(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:30:00Z',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'duration_minutes' => 90,
                ],
            ]);
    }

    public function testResponseMatchesOpenApiSchema(): void
    {
        $response = $this->postJson('/api/bookings', [
            'consultant_id' => '9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b',
            'customer_name' => 'Max Mustermann',
            'customer_email' => 'max.mustermann@example.com',
            'starts_at' => '2026-01-20T10:00:00Z',
            'ends_at' => '2026-01-20T11:00:00Z',
        ]);

        $response->assertStatus(201);

        $data = $response->json('data');

        $this->assertIsString($data['id']);
        $this->assertMatchesRegularExpression('/^[0-9a-f-]{36}$/', $data['id']);
        $this->assertSame('9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b', $data['consultant_id']);
        $this->assertSame('Max Mustermann', $data['customer_name']);
        $this->assertSame('max.mustermann@example.com', $data['customer_email']);
        $this->assertSame('2026-01-20T10:00:00Z', $data['starts_at']);
        $this->assertSame('2026-01-20T11:00:00Z', $data['ends_at']);
        $this->assertIsInt($data['duration_minutes']);
        $this->assertSame(60, $data['duration_minutes']);
        $this->assertIsString($data['created_at']);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}Z$/', $data['created_at']);
    }
}

