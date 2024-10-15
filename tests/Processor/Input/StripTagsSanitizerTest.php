<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\StripTagsSanitizer;
use PHPUnit\Framework\TestCase;

final class StripTagsSanitizerTest extends TestCase
{
    private StripTagsSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new StripTagsSanitizer();
    }

    public function testStripAllTags(): void
    {
        $input = '<p>test</p><script>alert("xss")</script>';
        $expected = 'testalert("xss")';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testAllowSpecificTags(): void
    {
        $this->sanitizer->configure(['allowedTags' => ['p']]);
        $input = '<p>test</p><script>alert("xss")</script>';
        $expected = '<p>test</p>alert("xss")';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testHandleNestedTags(): void
    {
        $this->sanitizer->configure(['allowedTags' => ['p', 'strong']]);
        $input = '<p>This is <strong>important</strong> and <em>emphasized</em></p>';
        $expected = '<p>This is <strong>important</strong> and emphasized</p>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testHandleInvalidHtml(): void
    {
        $input = '<p>Unclosed paragraph <strong>Bold text</p>';
        $expected = 'Unclosed paragraph Bold text';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testPreserveTextContent(): void
    {
        $input = '<div>Hello, <b>world</b>!</div>';
        $expected = 'Hello, world!';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testHandleEmptyInput(): void
    {
        $this->assertEquals('', $this->sanitizer->process(''));
    }

    public function testNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }
}
