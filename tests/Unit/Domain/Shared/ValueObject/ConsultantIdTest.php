<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\ConsultantId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class ConsultantIdTest extends TestCase
{
    public function testGeneratesValidUuid(): void
    {
        $id = ConsultantId::generate();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $id->toString()
        );
    }

    public function testGeneratesUniqueIds(): void
    {
        $id1 = ConsultantId::generate();
        $id2 = ConsultantId::generate();

        $this->assertFalse($id1->equals($id2));
    }

    /**
     * @dataProvider validUuidProvider
     */
    public function testCreatesFromValidUuidString(string $validUuid): void
    {
        $id = ConsultantId::fromString($validUuid);

        $this->assertSame($validUuid, $id->toString());
    }

    public static function validUuidProvider(): array
    {
        return [
            'uuid v4' => ['550e8400-e29b-41d4-a716-446655440000'],
            'another uuid' => ['9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b'],
            'uppercase' => ['550E8400-E29B-41D4-A716-446655440000'],
        ];
    }

    /**
     * @dataProvider invalidUuidProvider
     */
    public function testThrowsExceptionForInvalidUuid(string $invalidUuid): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid UUID format');

        ConsultantId::fromString($invalidUuid);
    }

    public static function invalidUuidProvider(): array
    {
        return [
            'empty string' => [''],
            'too short' => ['550e8400-e29b-41d4'],
            'no dashes' => ['550e8400e29b41d4a716446655440000'],
            'invalid chars' => ['550e8400-e29b-41d4-a716-44665544gggg'],
            'random text' => ['not-a-uuid'],
        ];
    }

    public function testEqualsReturnsTrueForSameId(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $id1 = ConsultantId::fromString($uuid);
        $id2 = ConsultantId::fromString($uuid);

        $this->assertTrue($id1->equals($id2));
    }

    public function testEqualsReturnsFalseForDifferentIds(): void
    {
        $id1 = ConsultantId::fromString('550e8400-e29b-41d4-a716-446655440000');
        $id2 = ConsultantId::fromString('9d4e8c2a-1b3d-4f5e-9a8c-7b6e5d4c3a2b');

        $this->assertFalse($id1->equals($id2));
    }
}

