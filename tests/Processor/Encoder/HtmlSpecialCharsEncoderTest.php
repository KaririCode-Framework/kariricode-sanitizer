<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Encoder;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Encoder\HtmlSpecialCharsEncoder;
use PHPUnit\Framework\TestCase;

final class HtmlSpecialCharsEncoderTest extends TestCase
{
    private HtmlSpecialCharsEncoder $encoder;

    protected function setUp(): void
    {
        $this->encoder = new HtmlSpecialCharsEncoder();
    }

    /**
     * @dataProvider htmlStringProvider
     */
    public function testProcessWithHtmlString(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->encoder->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function htmlStringProvider(): array
    {
        return [
            ['<p>Test</p>', '&lt;p&gt;Test&lt;/p&gt;'],
            ['"quoted" & \'single-quoted\'', '&quot;quoted&quot; &amp; &apos;single-quoted&apos;'], // Alterado para &apos;
            ['<a href="https://example.com">Link</a>', '&lt;a href=&quot;https://example.com&quot;&gt;Link&lt;/a&gt;'],
            ['Normal text', 'Normal text'],
        ];
    }

    public function testProcessWithNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->encoder->process(123);
    }
}
