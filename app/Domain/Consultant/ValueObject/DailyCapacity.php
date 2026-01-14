<?php

declare(strict_types=1);

namespace App\Domain\Consultant\ValueObject;

use InvalidArgumentException;

final readonly class DailyCapacity
{
    const int EIGHT_WORKING_HOURS_IN_MINUTES = 480;
    const int ONE_DAY_IN_MINUTES = 1440;


    private function __construct(
        private int $minutes
    ) {
        $this->validate();
    }


    public static function fromMinutes(int $minutes): self
    {
        return new self($minutes);
    }


    public static function eightHours(): self
    {
        return new self(self::EIGHT_WORKING_HOURS_IN_MINUTES);
    }


    public function minutes(): int
    {
        return $this->minutes;
    }


    public function isExceededBy(int $usedMinutes): bool
    {
        return $usedMinutes > $this->minutes;
    }


    public function remainingMinutes(int $usedMinutes): int
    {
        return max(0, $this->minutes - $usedMinutes);
    }


    private function validate(): void
    {
        if ($this->minutes <= 0) {
            throw new InvalidArgumentException('Daily capacity must be positive');
        }

        if ($this->minutes > self::ONE_DAY_IN_MINUTES) {
            throw new InvalidArgumentException('Daily capacity cannot exceed 24 hours (1440 minutes)');
        }
    }
}

