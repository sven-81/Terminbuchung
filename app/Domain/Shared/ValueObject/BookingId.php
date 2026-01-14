<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid;

final readonly class BookingId
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4()->toString());
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(BookingId $other): bool
    {
        return $this->value === $other->value;
    }

    private function validate(): void
    {
        if (!Uuid::isValid($this->value)) {
            throw new InvalidArgumentException("Invalid UUID format: {$this->value}");
        }
    }
}

