<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\ValidationTrait;
use PHPUnit\Framework\TestCase;

final class ValidationTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use ValidationTrait;

            public function callIsNotEmpty(string $input): bool
            {
                return $this->isNotEmpty($input);
            }

            public function callIsValidUtf8(string $input): bool
            {
                return $this->isValidUtf8($input);
            }

            public function callContainsPattern(string $input, string $pattern): bool
            {
                return $this->containsPattern($input, $pattern);
            }
        };
    }

    /**
     * @dataProvider isNotEmptyProvider
     */
    public function testIsNotEmpty(string $input, bool $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callIsNotEmpty($input));
    }

    /**
     * @dataProvider isValidUtf8Provider
     */
    public function testIsValidUtf8(string $input, bool $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callIsValidUtf8($input));
    }

    /**
     * @dataProvider containsPatternProvider
     */
    public function testContainsPattern(string $input, string $pattern, bool $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callContainsPattern($input, $pattern));
    }

    public static function isNotEmptyProvider(): array
    {
        return [
            'non empty string' => ['test', true],
            'empty string' => ['', false],
            'spaces only' => ['   ', false],
            'tabs and newlines' => ["\t\n", false],
            'zero as string' => ['0', true],
            'with spaces' => [' test ', true],
        ];
    }

    public static function isValidUtf8Provider(): array
    {
        return [
            'ascii string' => ['Hello World', true],
            'utf8 string' => ['áéíóú', true],
            'emojis' => ['😀👍🎉', true],
            'empty string' => ['', true],
            'valid mixed content' => ['Hello 世界', true],
        ];
    }

    public static function containsPatternProvider(): array
    {
        return [
            'simple pattern' => ['test123', '/\d+/', true],
            'email pattern' => ['test@example.com', '/^[\w\-\.]+@([\w\-]+\.)+[\w\-]{2,}$/', true],
            'no match' => ['abcdef', '/\d+/', false],
            'complex pattern' => ['ABC-123', '/^[A-Z]+-\d+$/', true],
            'empty string' => ['', '/.*/', true],
        ];
    }
}
