<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\BookingId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class BookingIdTest extends TestCase
{
    public function testGeneratesValidUuid(): void
    {
        $id = BookingId::generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $id->toString()
        );
    }


    public function testGeneratesUniqueIds(): void
    {
        $id1 = BookingId::generate();
        $id2 = BookingId::generate();

        $this->assertFalse($id1->equals($id2));
    }


    /**
     * @dataProvider validUuidProvider
     */
    public function testCreatesFromValidUuidString(string $validUuid): void
    {
        $id = BookingId::fromString($validUuid);

        $this->assertSame($validUuid, $id->toString());
    }


    public static function validUuidProvider(): array
    {
        return [
            'uuid v4' => ['8c7d6e5f-4a3b-2c1d-0e9f-8a7b6c5d4e3f'],
            'another uuid' => ['660e8400-e29b-41d4-a716-446655440001'],
            'uppercase' => ['8C7D6E5F-4A3B-2C1D-0E9F-8A7B6C5D4E3F'],
        ];
    }


    /**
     * @dataProvider invalidUuidProvider
     */
    public function testThrowsExceptionForInvalidUuid(string $invalidUuid): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID format');

        BookingId::fromString($invalidUuid);
    }


    public static function invalidUuidProvider(): array
    {
        return [
            'empty string' => [''],
            'too short' => ['8c7d6e5f-4a3b'],
            'no dashes' => ['8c7d6e5f4a3b2c1d0e9f8a7b6c5d4e3f'],
            'invalid chars' => ['8c7d6e5f-4a3b-2c1d-0e9f-8a7b6c5dXXXX'],
            'random text' => ['booking-123'],
        ];
    }


    public function testEqualsReturnsTrueForSameId(): void
    {
        $uuid = '8c7d6e5f-4a3b-2c1d-0e9f-8a7b6c5d4e3f';
        $id1 = BookingId::fromString($uuid);
        $id2 = BookingId::fromString($uuid);

        $this->assertTrue($id1->equals($id2));
    }


    public function testEqualsReturnsFalseForDifferentIds(): void
    {
        $id1 = BookingId::fromString('8c7d6e5f-4a3b-2c1d-0e9f-8a7b6c5d4e3f');
        $id2 = BookingId::fromString('660e8400-e29b-41d4-a716-446655440001');

        $this->assertFalse($id1->equals($id2));
    }
}

