<?php

declare(strict_types=1);

namespace App\Domain\Consultant;

use App\Domain\Consultant\ValueObject\DailyCapacity;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use DateTimeImmutable;

final class Consultant
{
    private function __construct(
        private ConsultantId $id,
        private string $name,
        private Email $email,
        private DailyCapacity $dailyCapacity,
        private DateTimeImmutable $createdAt
    ) {
    }

    public static function create(
        ConsultantId $id,
        string $name,
        Email $email,
        DailyCapacity $dailyCapacity
    ): self {
        return new self(
            $id,
            $name,
            $email,
            $dailyCapacity,
            new DateTimeImmutable()
        );
    }

    public function id(): ConsultantId
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function dailyCapacity(): DailyCapacity
    {
        return $this->dailyCapacity;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }
}
