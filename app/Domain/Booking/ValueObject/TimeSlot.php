<?php

declare(strict_types=1);

namespace App\Domain\Booking\ValueObject;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class TimeSlot
{
    private function __construct(
        private DateTimeImmutable $startsAt,
        private DateTimeImmutable $endsAt
    ) {
        $this->validate();
    }


    public static function fromDateTimes(
        DateTimeImmutable $startsAt,
        DateTimeImmutable $endsAt
    ): self {
        return new self($startsAt, $endsAt);
    }


    public function startsAt(): DateTimeImmutable
    {
        return $this->startsAt;
    }


    public function endsAt(): DateTimeImmutable
    {
        return $this->endsAt;
    }


    public function durationInMinutes(): int
    {
        $diff = $this->endsAt->getTimestamp() - $this->startsAt->getTimestamp();

        return (int) ($diff / 60);
    }


    public function isOnDate(DateTimeImmutable $date): bool
    {
        return $this->startsAt->format('Y-m-d') === $date->format('Y-m-d');
    }


    public function overlapsWith(TimeSlot $other): bool
    {
        return $this->startsAt < $other->endsAt && $this->endsAt > $other->startsAt;
    }


    public function isInPast(): bool
    {
        return $this->startsAt < new DateTimeImmutable('now');
    }


    private function validate(): void
    {
        if ($this->startsAt >= $this->endsAt) {
            throw new InvalidArgumentException('Start time must be before end time');
        }
    }
}

