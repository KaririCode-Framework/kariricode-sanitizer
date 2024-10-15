<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Cleaner;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Cleaner\UrlAddressCleaner;
use PHPUnit\Framework\TestCase;

final class UrlAddressCleanerTest extends TestCase
{
    private UrlAddressCleaner $cleaner;

    protected function setUp(): void
    {
        $this->cleaner = new UrlAddressCleaner();
    }

    /**
     * @dataProvider validUrlProvider
     */
    public function testProcessWithValidUrl(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->cleaner->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function validUrlProvider(): array
    {
        return [
            ['https://www.example.com', 'https://www.example.com'],
            ['http://example.com/path?query=value', 'http://example.com/path?query=value'],
            [' https://www.example.com ', 'https://www.example.com'],
            ['www.example.com', 'www.example.com'],
        ];
    }

    /**
     * @dataProvider invalidUrlProvider
     */
    public function testProcessWithInvalidUrl(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->cleaner->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function invalidUrlProvider(): array
    {
        return [
            ['http://', 'http://'],
            ['https://', 'https://'],
            ['ftp:/example.com', 'ftp:/example.com'],
        ];
    }

    public function testProcessWithNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->cleaner->process(123);
    }
}
