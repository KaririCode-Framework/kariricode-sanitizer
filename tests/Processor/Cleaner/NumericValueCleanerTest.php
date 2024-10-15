<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Cleaner;

use KaririCode\Sanitizer\Processor\Cleaner\NumericValueCleaner;
use PHPUnit\Framework\TestCase;

final class NumericValueCleanerTest extends TestCase
{
    private NumericValueCleaner $cleaner;

    protected function setUp(): void
    {
        $this->cleaner = new NumericValueCleaner();
    }

    /**
     * @dataProvider validNumericProvider
     */
    public function testProcessWithValidNumeric(mixed $input, string $expected): void
    {
        $this->assertSame($expected, $this->cleaner->process($input));
    }

    /**
     * @return array<array{mixed, string}>
     */
    public static function validNumericProvider(): array
    {
        return [
            [123, '123'],
            [123.45, '123.45'],
            ['123', '123'],
            ['123.45', '123.45'],
            [' 123.45 ', '123.45'],
            ['-123.45', '-123.45'],
            ['+123.45', '+123.45'],
        ];
    }

    /**
     * @dataProvider invalidNumericProvider
     */
    public function testProcessWithInvalidNumeric(mixed $input, string $expected): void
    {
        $this->assertSame($expected, $this->cleaner->process($input));
    }

    /**
     * @return array<array{mixed, string}>
     */
    public static function invalidNumericProvider(): array
    {
        return [
            ['not a number', '0'],
            ['123abc', '123'],
            ['abc123', '123'],
            ['', '0'],
        ];
    }
}
