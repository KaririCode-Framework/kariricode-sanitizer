<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;
use PHPUnit\Framework\TestCase;

final class WhitespaceSanitizerTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use WhitespaceSanitizerTrait;

            public function callRemoveAllWhitespace(string $input): string
            {
                return $this->removeAllWhitespace($input);
            }

            public function callNormalizeWhitespace(string $input): string
            {
                return $this->normalizeWhitespace($input);
            }

            public function callTrimWhitespace(string $input): string
            {
                return $this->trimWhitespace($input);
            }
        };
    }

    /**
     * @dataProvider removeAllWhitespaceProvider
     */
    public function testRemoveAllWhitespace(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callRemoveAllWhitespace($input));
    }

    /**
     * @dataProvider normalizeWhitespaceProvider
     */
    public function testNormalizeWhitespace(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callNormalizeWhitespace($input));
    }

    /**
     * @dataProvider trimWhitespaceProvider
     */
    public function testTrimWhitespace(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callTrimWhitespace($input));
    }

    public static function removeAllWhitespaceProvider(): array
    {
        return [
            'spaces' => ['hello world', 'helloworld'],
            'tabs and spaces' => ["hello\tworld", 'helloworld'],
            'newlines' => ["hello\nworld", 'helloworld'],
            'multiple spaces' => ['hello    world', 'helloworld'],
            'complex whitespace' => ["hello\n\t world", 'helloworld'],
            'empty string' => ['', ''],
            'only whitespace' => ['   ', ''],
        ];
    }

    public static function normalizeWhitespaceProvider(): array
    {
        return [
            'multiple spaces' => ['hello    world', 'hello world'],
            'tabs' => ["hello\tworld", 'hello world'],
            'newlines' => ["hello\nworld", 'hello world'],
            'mixed whitespace' => ["hello\n\t  world", 'hello world'],
            'empty string' => ['', ''],
            'only whitespace' => ['   ', ' '],
            'leading/trailing spaces' => ['  hello  world  ', ' hello world '],
        ];
    }

    public static function trimWhitespaceProvider(): array
    {
        return [
            'leading spaces' => ['   hello', 'hello'],
            'trailing spaces' => ['hello   ', 'hello'],
            'both sides' => ['   hello   ', 'hello'],
            'tabs' => ["\thello\t", 'hello'],
            'newlines' => ["\nhello\n", 'hello'],
            'mixed whitespace' => [" \t\nhello\t \n", 'hello'],
            'empty string' => ['', ''],
            'only whitespace' => ['   ', ''],
        ];
    }
}
