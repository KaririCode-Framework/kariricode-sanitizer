<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;
use PHPUnit\Framework\TestCase;

final class NormalizeLineBreaksSanitizerTest extends TestCase
{
    private NormalizeLineBreaksSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new NormalizeLineBreaksSanitizer();
    }

    /**
     * @dataProvider lineBreaksProvider
     */
    public static function testBasicNormalization(string $input, string $expected): void
    {
        $sanitizer = new NormalizeLineBreaksSanitizer();
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testCustomLineEnding(): void
    {
        $this->sanitizer->configure(['lineEnding' => 'windows']);
        $input = "Line1\nLine2\rLine3\r\nLine4";
        $expected = "Line1\r\nLine2\r\nLine3\r\nLine4";

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testInvalidLineEndingConfiguration(): void
    {
        $this->sanitizer->configure(['lineEnding' => 'invalid']);
        $input = "Line1\nLine2";

        $this->assertSame("Line1\nLine2", $this->sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function lineBreaksProvider(): array
    {
        return [
            'unix line endings' => [
                "Line1\nLine2\nLine3",
                "Line1\nLine2\nLine3",
            ],
            'windows line endings' => [
                "Line1\r\nLine2\r\nLine3",
                "Line1\nLine2\nLine3",
            ],
            'mac line endings' => [
                "Line1\rLine2\rLine3",
                "Line1\nLine2\nLine3",
            ],
            'mixed line endings' => [
                "Line1\rLine2\r\nLine3\nLine4",
                "Line1\nLine2\nLine3\nLine4",
            ],
            'no line endings' => [
                'SingleLine',
                'SingleLine',
            ],
            'multiple consecutive line endings' => [
                "Line1\n\n\nLine2",
                "Line1\n\n\nLine2",
            ],
        ];
    }
}
