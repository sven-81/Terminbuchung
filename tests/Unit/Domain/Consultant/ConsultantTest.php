<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Consultant;

use App\Domain\Consultant\Consultant;
use App\Domain\Consultant\ValueObject\DailyCapacity;
use App\Domain\Shared\ValueObject\ConsultantId;
use App\Domain\Shared\ValueObject\Email;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ConsultantTest extends TestCase
{
    public function testCreatesConsultant(): void
    {
        $id = ConsultantId::generate();
        $name = 'Dr. Anna Müller';
        $email = Email::fromString('anna@example.com');
        $capacity = DailyCapacity::fromMinutes(480);

        $consultant = Consultant::create($id, $name, $email, $capacity);

        $this->assertTrue($consultant->id()->equals($id));
        $this->assertSame($name, $consultant->name());
        $this->assertTrue($consultant->email()->equals($email));
        $this->assertSame(480, $consultant->dailyCapacity()->minutes());
        $this->assertInstanceOf(DateTimeImmutable::class, $consultant->createdAt());
    }


    public function testCreatedAtIsSetAutomatically(): void
    {
        $before = new DateTimeImmutable();

        $consultant = Consultant::create(
            ConsultantId::generate(),
            'Test',
            Email::fromString('test@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $after = new DateTimeImmutable();

        $this->assertGreaterThanOrEqual($before, $consultant->createdAt());
        $this->assertLessThanOrEqual($after, $consultant->createdAt());
    }


    public function testDifferentConsultantsHaveDifferentIds(): void
    {
        $consultant1 = Consultant::create(
            ConsultantId::generate(),
            'Consultant 1',
            Email::fromString('c1@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $consultant2 = Consultant::create(
            ConsultantId::generate(),
            'Consultant 2',
            Email::fromString('c2@example.com'),
            DailyCapacity::fromMinutes(480)
        );

        $this->assertFalse($consultant1->id()->equals($consultant2->id()));
    }
}
