<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\UrlSanitizer;
use PHPUnit\Framework\TestCase;

final class UrlSanitizerTest extends TestCase
{
    private UrlSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new UrlSanitizer();
    }

    /**
     * @dataProvider urlDataProvider
     */
    public static function testUrlSanitization(string $input, array $config, string $expected): void
    {
        $sanitizer = new UrlSanitizer();
        $sanitizer->configure($config);
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testCustomProtocol(): void
    {
        $this->sanitizer->configure([
            'enforceProtocol' => true,
            'defaultProtocol' => 'http://',
        ]);

        $input = 'example.com';
        $expected = 'http://example.com';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function urlDataProvider(): array
    {
        return [
            'basic url' => [
                'example.com',
                ['enforceProtocol' => true],
                'https://example.com',
            ],
            'with protocol' => [
                'https://example.com',
                ['enforceProtocol' => true],
                'https://example.com',
            ],
            'multiple slashes' => [
                'https://example.com//path///to//resource',
                [],
                'https://example.com/path/to/resource',
            ],
            'with trailing slash' => [
                'https://example.com/',
                ['removeTrailingSlash' => true],
                'https://example.com',
            ],
            'keep trailing slash' => [
                'https://example.com/',
                ['removeTrailingSlash' => false],
                'https://example.com/',
            ],
            'no protocol enforcement' => [
                'example.com',
                ['enforceProtocol' => false],
                'example.com',
            ],
            'with spaces' => [
                '  https://example.com  ',
                [],
                'https://example.com',
            ],
        ];
    }
}
