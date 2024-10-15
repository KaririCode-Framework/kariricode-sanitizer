<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Cleaner;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Cleaner\EmailAddressCleaner;
use PHPUnit\Framework\TestCase;

final class EmailAddressCleanerTest extends TestCase
{
    private EmailAddressCleaner $cleaner;

    protected function setUp(): void
    {
        $this->cleaner = new EmailAddressCleaner();
    }

    /**
     * @dataProvider validEmailProvider
     */
    public function testProcessWithValidEmail(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->cleaner->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function validEmailProvider(): array
    {
        return [
            ['test@example.com', 'test@example.com'],
            ['test+filter@example.com', 'test+filter@example.com'],
            [' test@example.com ', 'test@example.com'],
            ['TEST@EXAMPLE.COM', 'TEST@EXAMPLE.COM'],
        ];
    }

    /**
     * @dataProvider invalidEmailProvider
     */
    public function testProcessWithInvalidEmail(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->cleaner->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function invalidEmailProvider(): array
    {
        return [
            ['not an email', 'notanemail'],
            ['test@', 'test@'],
            ['@example.com', '@example.com'],
            ['test@example', 'test@example'],
        ];
    }

    public function testProcessWithNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->cleaner->process(123);
    }
}
