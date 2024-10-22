<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\NumericSanitizer;
use PHPUnit\Framework\TestCase;

final class NumericSanitizerTest extends TestCase
{
    private NumericSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new NumericSanitizer();
    }

    /**
     * @dataProvider numericDataProvider
     */
    public static function testNumericSanitization(string $input, array $config, string $expected): void
    {
        $sanitizer = new NumericSanitizer();
        $sanitizer->configure($config);
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testCustomDecimalSeparator(): void
    {
        $this->sanitizer->configure([
            'allowDecimal' => true,
            'decimalSeparator' => ',',
        ]);

        $input = '123,45';
        $expected = '123,45';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function numericDataProvider(): array
    {
        return [
            'basic integer' => [
                '123',
                [],
                '123',
            ],
            'with decimals allowed' => [
                '123.45',
                ['allowDecimal' => true],
                '123.45',
            ],
            'with decimals disabled' => [
                '123.45',
                ['allowDecimal' => false],
                '12345',
            ],
            'negative number allowed' => [
                '-123.45',
                ['allowDecimal' => true, 'allowNegative' => true],
                '-123.45',
            ],
            'negative number disabled' => [
                '-123.45',
                ['allowDecimal' => true, 'allowNegative' => false],
                '123.45',
            ],
            'mixed characters' => [
                'abc123.45xyz',
                ['allowDecimal' => true],
                '123.45',
            ],
            'multiple decimal points' => [
                '123.45.67',
                ['allowDecimal' => true],
                '123.4567',
            ],
            'only non-numeric' => [
                'abc',
                [],
                '',
            ],
            'empty string' => [
                '',
                [],
                '',
            ],
        ];
    }
}
