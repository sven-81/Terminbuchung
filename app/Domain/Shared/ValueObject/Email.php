<?php
}
    }
        }
            throw new InvalidArgumentException("Invalid email format: {$this->value}");
        if (!filter_var($this->value, FILTER_VALIDATE_EMAIL)) {
    {
    private function validate(): void

    }
        return $this->value === $other->value;
    {
    public function equals(Email $other): bool

    }
        return $this->value;
    {
    public function toString(): string

    }
        return new self($email);
    {
    public static function fromString(string $email): self

    }
        $this->validate();
    ) {
        private string $value
    private function __construct(
{
final readonly class Email

use InvalidArgumentException;

namespace App\Domain\Shared\ValueObject;

declare(strict_types=1);


