<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObject;

use InvalidArgumentException;

final readonly class Email
{
    private function __construct(
        private string $value
    ) {
        $this->validate();
    }


    public static function fromString(string $email): self
    {
        return new self($email);
    }


    public function toString(): string
    {
        return $this->value;
    }


    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }


    private function validate(): void
    {
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email format: {$this->value}");
        }
    }
}
