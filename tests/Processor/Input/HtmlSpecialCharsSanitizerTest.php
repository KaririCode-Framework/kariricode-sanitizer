<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use PHPUnit\Framework\TestCase;

final class HtmlSpecialCharsSanitizerTest extends TestCase
{
    private HtmlSpecialCharsSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new HtmlSpecialCharsSanitizer();
    }

    /**
     * @dataProvider htmlSpecialCharsProvider
     */
    public static function testBasicSanitization(string $input, string $expected): void
    {
        $sanitizer = new HtmlSpecialCharsSanitizer();
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testCustomConfiguration(): void
    {
        $this->sanitizer->configure([
            'flags' => ENT_NOQUOTES,
            'encoding' => 'ISO-8859-1',
            'doubleEncode' => false,
        ]);

        $input = '<div class="test">&amp;test</div>';
        $expected = '&lt;div class="test"&gt;&amp;test&lt;/div&gt;';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function htmlSpecialCharsProvider(): array
    {
        return [
            'basic html' => [
                '<p>Test</p>',
                '&lt;p&gt;Test&lt;/p&gt;',
            ],
            'attributes' => [
                '<div class="test">Content</div>',
                '&lt;div class=&quot;test&quot;&gt;Content&lt;/div&gt;',
            ],
            'special chars' => [
                '& < > " \'',
                '&amp; &lt; &gt; &quot; &apos;',
            ],
            'already encoded' => [
                '&amp; &lt; &gt;',
                '&amp;amp; &amp;lt; &amp;gt;',
            ],
            'mixed content' => [
                '<p class="test">Test & Demo</p>',
                '&lt;p class=&quot;test&quot;&gt;Test &amp; Demo&lt;/p&gt;',
            ],
        ];
    }
}
