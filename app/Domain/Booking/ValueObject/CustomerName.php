<?php

declare(strict_types=1);

namespace App\Domain\Booking\ValueObject;

use InvalidArgumentException;

final readonly class CustomerName
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }

    public static function fromString(string $name): self
    {
        return new self(trim($name));
    }

    public function toString(): string
    {
        return $this->value;
    }

    private function validate(): void
    {
        if (empty($this->value)) {
            throw new InvalidArgumentException('Customer name cannot be empty');
        }

        if (mb_strlen($this->value) > 255) {
            throw new InvalidArgumentException('Customer name cannot exceed 255 characters');
        }
    }
}

