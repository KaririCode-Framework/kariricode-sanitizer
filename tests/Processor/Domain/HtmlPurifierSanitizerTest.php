<?php

declare(strict_types=1);

namespace KaririCode\Tests\Sanitizer\Processor\Domain;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;
use PHPUnit\Framework\TestCase;

class HtmlPurifierSanitizerTest extends TestCase
{
    private HtmlPurifierSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new HtmlPurifierSanitizer();
    }

    public function testProcessRemovesDisallowedTags(): void
    {
        $input = '<p>This is a <script>alert("test");</script> test.</p>';
        $expected = '<p>This is a  test.</p>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testProcessRemovesDisallowedAttributes(): void
    {
        $input = '<a href="https://example.com" onclick="alert(\'test\')">Link</a>';
        $expected = '<a href="https://example.com">Link</a>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testProcessRemovesHtmlComments(): void
    {
        $input = '<p>This is a <!-- comment --> test.</p>';
        $expected = '<p>This is a  test.</p>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testConfigureChangesAllowedTags(): void
    {
        $this->sanitizer->configure(['allowedTags' => ['p', 'strong']]);
        $input = '<p>This is <strong>bold</strong> and <em>italic</em>.</p>';
        $expected = '<p>This is <strong>bold</strong> and italic.</p>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testConfigureChangesAllowedAttributes(): void
    {
        $this->sanitizer->configure(['allowedAttributes' => ['class' => ['p']]]);
        $input = '<p class="test" id="para">This is a test.</p>';
        $expected = '<p class="test">This is a test.</p>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }

    public function testProcessHandlesNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public function testProcessHandlesInvalidHtml(): void
    {
        $input = '<p>This is an unclosed paragraph';
        $expected = '<p>This is an unclosed paragraph</p>';
        $this->assertEquals($expected, $this->sanitizer->process($input));
    }
}
