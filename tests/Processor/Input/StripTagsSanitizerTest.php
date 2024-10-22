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

    /**
     * @dataProvider stripTagsProvider
     */
    public function testBasicStripping(string $input, array $config, string $expected): void
    {
        $this->sanitizer->configure($config);
        self::assertSame($expected, $this->sanitizer->process($input));
    }

    public function testWithAllowedTagsAndAttributes(): void
    {
        $this->sanitizer->configure([
            'allowedTags' => ['p', 'div'],
            'keepSafeAttributes' => true,
            'safeAttributes' => ['class', 'id'],
        ]);

        $input = '<p class="test" onclick="alert()">Text</p><script>alert()</script>';
        $expected = '<p class="test">Text</p>';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testWithoutSafeAttributes(): void
    {
        $this->sanitizer->configure([
            'allowedTags' => ['p'],
            'keepSafeAttributes' => false,
        ]);

        $input = '<p class="test" id="demo">Text</p>';
        $expected = '<p>Text</p>';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function stripTagsProvider(): array
    {
        return [
            'no tags' => [
                'Plain text content',
                [],
                'Plain text content',
            ],
            'simple tags' => [
                '<p>Paragraph</p><div>Division</div>',
                [],
                'ParagraphDivision',
            ],
            'nested tags' => [
                '<div><p>Nested content</p></div>',
                ['allowedTags' => ['div']],
                '<div>Nested content</div>',
            ],
            'mixed content' => [
                '<p>Text</p><script>alert()</script><style>.css{}</style>',
                ['allowedTags' => ['p']],
                '<p>Text</p>',
            ],
            'with attributes' => [
                '<p class="test" id="demo">Text</p>',
                [
                    'allowedTags' => ['p'],
                    'keepSafeAttributes' => true,
                    'safeAttributes' => ['class'],
                ],
                '<p class="test">Text</p>',
            ],
        ];
    }
}
