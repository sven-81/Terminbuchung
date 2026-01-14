<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Booking\ValueObject;

use App\Domain\Booking\ValueObject\CustomerName;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class CustomerNameTest extends TestCase
{
    /**
     * @dataProvider validNameProvider
     */
    public function testCreatesFromValidString(string $input, string $expected): void
    {
        $name = CustomerName::fromString($input);

        $this->assertSame($expected, $name->toString());
    }


    public static function validNameProvider(): array
    {
        return [
            'simple name' => ['Max Mustermann', 'Max Mustermann'],
            'with spaces trimmed' => ['  Max Mustermann  ', 'Max Mustermann'],
            'single word' => ['Max', 'Max'],
            'with special chars' => ['Max Müller-Schmidt', 'Max Müller-Schmidt'],
            'long name' => ['Dr. Maximilian Alexander von Mustermann', 'Dr. Maximilian Alexander von Mustermann'],
            'with numbers' => ['John Doe 123', 'John Doe 123'],
        ];
    }


    /**
     * @dataProvider invalidNameProvider
     */
    public function testThrowsExceptionForInvalidName(string $invalidName, string $expectedMessage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage($expectedMessage);

        CustomerName::fromString($invalidName);
    }


    public static function invalidNameProvider(): array
    {
        return [
            'empty string' => ['', 'Customer name cannot be empty'],
            'only spaces' => ['   ', 'Customer name cannot be empty'],
            'too long' => [str_repeat('a', 256), 'Customer name cannot exceed 255 characters'],
        ];
    }


    public function testTrimsWhitespace(): void
    {
        $name = CustomerName::fromString('  Max Mustermann  ');

        $this->assertSame('Max Mustermann', $name->toString());
    }


    public function testAcceptsExactly255Characters(): void
    {
        $longName = str_repeat('a', 255);
        $name = CustomerName::fromString($longName);

        $this->assertSame($longName, $name->toString());
    }


    public function testHandlesSpecialCharacters(): void
    {
        $name = CustomerName::fromString('Max Müller äöü ß');

        $this->assertSame('Max Müller äöü ß', $name->toString());
    }
}
