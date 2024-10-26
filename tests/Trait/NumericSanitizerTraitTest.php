<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\NumericSanitizerTrait;
use PHPUnit\Framework\TestCase;

final class NumericSanitizerTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use NumericSanitizerTrait;

            public function callExtractNumbers(string $input): string
            {
                return $this->extractNumbers($input);
            }

            public function callPreserveDecimalPoint(string $input, string $decimalPoint = '.'): string
            {
                return $this->preserveDecimalPoint($input, $decimalPoint);
            }
        };
    }

    /**
     * @dataProvider extractNumbersProvider
     */
    public function testExtractNumbers(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callExtractNumbers($input));
    }

    /**
     * @dataProvider preserveDecimalPointProvider
     */
    public function testPreserveDecimalPoint(string $input, string $decimalPoint, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callPreserveDecimalPoint($input, $decimalPoint));
    }

    public static function extractNumbersProvider(): array
    {
        return [
            'only numbers' => ['123456', '123456'],
            'mixed content' => ['abc123def456', '123456'],
            'with special chars' => ['!@#123$%^456', '123456'],
            'empty string' => ['', ''],
            'no numbers' => ['abcdef', ''],
            'with spaces' => ['123 456', '123456'],
        ];
    }

    public static function preserveDecimalPointProvider(): array
    {
        return [
            'simple decimal' => ['123.456', '.', '123.456'],
            'custom decimal point' => ['123,456', ',', '123,456'],
            'multiple decimal points' => ['123.456.789', '.', '123.456789'],
            'no decimal point' => ['123456', '.', '123456'],
            'only decimal point' => ['.', '.', '.'],
            'decimal at start' => ['.123', '.', '.123'],
            'decimal at end' => ['123.', '.', '123.'],
        ];
    }
}
