<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Remover;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Remover\HtmlTagRemover;
use PHPUnit\Framework\TestCase;

final class HtmlTagRemoverTest extends TestCase
{
    private HtmlTagRemover $remover;

    protected function setUp(): void
    {
        $this->remover = new HtmlTagRemover();
    }

    /**
     * @dataProvider htmlStringProvider
     */
    public function testProcessWithHtmlString(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->remover->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function htmlStringProvider(): array
    {
        return [
            ['<p>Test</p>', 'Test'],
            ['<a href="https://example.com">Link</a>', 'Link'],
            ['<script>alert("XSS");</script>', 'alert("XSS");'],
            ['Normal text', 'Normal text'],
        ];
    }

    public function testProcessWithAllowedTags(): void
    {
        $this->remover->configure(['allowedTags' => ['p', 'a']]);
        $input = '<p>Test</p><a href="#">Link</a><script>alert("XSS");</script>';
        $expected = '<p>Test</p><a href="#">Link</a>alert("XSS");';

        $this->assertSame($expected, $this->remover->process($input));
    }

    public function testProcessWithNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->remover->process(123);
    }
}
