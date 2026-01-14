<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Shared\ValueObject;

use App\Domain\Shared\ValueObject\Email;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    /**
     * @dataProvider validEmailProvider
     */
    public function testCreatesEmailFromValidString(string $validEmail): void
    {
        $email = Email::fromString($validEmail);

        $this->assertSame($validEmail, $email->toString());
    }

    public static function validEmailProvider(): array
    {
        return [
            'simple email' => ['test@example.com'],
            'with subdomain' => ['user@mail.example.com'],
            'with plus' => ['user+tag@example.com'],
            'with dash' => ['first-last@example.com'],
            'with numbers' => ['user123@example456.com'],
            'short domain' => ['a@b.co'],
        ];
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testThrowsExceptionForInvalidEmail(string $invalidEmail): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid email format');

        Email::fromString($invalidEmail);
    }

    public static function invalidEmailProvider(): array
    {
        return [
            'missing @' => ['testexample.com'],
            'missing domain' => ['test@'],
            'missing local part' => ['@example.com'],
            'double @' => ['test@@example.com'],
            'spaces' => ['test @example.com'],
            'empty string' => [''],
            'only @' => ['@'],
            'no dot in domain' => ['test@example'],
        ];
    }

    public function testEqualsReturnsTrueForSameEmail(): void
    {
        $email1 = Email::fromString('test@example.com');
        $email2 = Email::fromString('test@example.com');

        $this->assertTrue($email1->equals($email2));
    }

    public function testEqualsReturnsFalseForDifferentEmails(): void
    {
        $email1 = Email::fromString('test1@example.com');
        $email2 = Email::fromString('test2@example.com');

        $this->assertFalse($email1->equals($email2));
    }

    public function testEqualsIsCaseSensitive(): void
    {
        $email1 = Email::fromString('Test@example.com');
        $email2 = Email::fromString('test@example.com');

        $this->assertFalse($email1->equals($email2));
    }
}
