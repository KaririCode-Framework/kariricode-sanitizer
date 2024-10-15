<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Remover;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Remover\WhitespaceRemover;
use PHPUnit\Framework\TestCase;

final class WhitespaceRemoverTest extends TestCase
{
    private WhitespaceRemover $remover;

    protected function setUp(): void
    {
        $this->remover = new WhitespaceRemover();
    }

    /**
     * @dataProvider whitespaceStringProvider
     */
    public function testProcessWithWhitespaceString(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->remover->process($input));
    }

    /**
     * @return array<array{string, string}>
     */
    public static function whitespaceStringProvider(): array
    {
        return [
            ['  Test  ', 'Test'],
            ["\t\tTest\t\t", 'Test'],
            ["\nTest\n", 'Test'],
            [" \t\n\r\0\x0BTest \t\n\r\0\x0B", 'Test'],
        ];
    }

    public function testProcessWithCustomCharlist(): void
    {
        $this->remover->configure(['charlist' => 'a']);
        $this->assertSame('Test', $this->remover->process('aaaTestaaa'));
    }

    public function testProcessWithNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->remover->process(123);
    }
}
