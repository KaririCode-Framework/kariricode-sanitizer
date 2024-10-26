<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\UrlSanitizerTrait;
use PHPUnit\Framework\TestCase;

final class UrlSanitizerTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use UrlSanitizerTrait;

            public function callNormalizeProtocol(string $url, string $defaultProtocol = 'https://'): string
            {
                return $this->normalizeProtocol($url, $defaultProtocol);
            }

            public function callNormalizeSlashes(string $url): string
            {
                return $this->normalizeSlashes($url);
            }
        };
    }

    /**
     * @dataProvider normalizeProtocolProvider
     */
    public function testNormalizeProtocol(string $input, string $defaultProtocol, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callNormalizeProtocol($input, $defaultProtocol));
    }

    /**
     * @dataProvider normalizeSlashesProvider
     */
    public function testNormalizeSlashes(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callNormalizeSlashes($input));
    }

    public static function normalizeProtocolProvider(): array
    {
        return [
            'no protocol' => ['example.com', 'https://', 'https://example.com'],
            'with http' => ['http://example.com', 'https://', 'http://example.com'],
            'with https' => ['https://example.com', 'http://', 'https://example.com'],
            'with ftp' => ['ftp://example.com', 'https://', 'ftp://example.com'],
            'with sftp' => ['sftp://example.com', 'https://', 'sftp://example.com'],
            'custom protocol' => ['example.com', 'http://', 'http://example.com'],
            'empty string' => ['', 'https://', 'https://'],
            'with extra slashes' => ['/example.com', 'https://', 'https://example.com'],
        ];
    }

    public static function normalizeSlashesProvider(): array
    {
        return [
            'normal url' => ['https://example.com/path', 'https://example.com/path'],
            'multiple slashes' => ['https://example.com//path', 'https://example.com/path'],
            'preserve protocol slashes' => ['https://example.com', 'https://example.com'],
            'complex path' => ['https://example.com/path//to///resource', 'https://example.com/path/to/resource'],
            'empty string' => ['', ''],
            'only slashes' => ['////', '/'],
            'mixed slashes' => ['http:///example.com//path', 'http://example.com/path'],
        ];
    }
}
