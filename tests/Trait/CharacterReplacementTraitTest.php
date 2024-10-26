<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\CharacterReplacementTrait;
use PHPUnit\Framework\TestCase;

final class CharacterReplacementTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use CharacterReplacementTrait;

            public function callReplaceConsecutiveCharacters(string $input, string $char, string $replacement): string
            {
                return $this->replaceConsecutiveCharacters($input, $char, $replacement);
            }

            public function callReplaceMultipleCharacters(string $input, array $replacements): string
            {
                return $this->replaceMultipleCharacters($input, $replacements);
            }
        };
    }

    /**
     * @dataProvider replaceConsecutiveCharactersProvider
     */
    public function testReplaceConsecutiveCharacters(string $input, string $char, string $replacement, string $expected): void
    {
        $this->assertSame(
            $expected,
            $this->traitObject->callReplaceConsecutiveCharacters($input, $char, $replacement)
        );
    }

    /**
     * @dataProvider replaceMultipleCharactersProvider
     */
    public function testReplaceMultipleCharacters(string $input, array $replacements, string $expected): void
    {
        $this->assertSame(
            $expected,
            $this->traitObject->callReplaceMultipleCharacters($input, $replacements)
        );
    }

    public static function replaceConsecutiveCharactersProvider(): array
    {
        return [
            'basic replacement' => ['hello....world', '.', '.', 'hello.world'],
            'multiple occurrences' => ['hi....there....now', '.', '.', 'hi.there.now'],
            'no consecutive chars' => ['hello.world', '.', '.', 'hello.world'],
            'empty string' => ['', '.', '.', ''],
            'special characters' => ['test***case', '*', '_', 'test_case'],
            'with spaces' => ['hello   world', ' ', ' ', 'hello world'],
        ];
    }

    public static function replaceMultipleCharactersProvider(): array
    {
        return [
            'basic replacements' => [
                'hello world',
                ['hello' => 'hi', 'world' => 'earth'],
                'hi earth',
            ],
            'no matches' => [
                'test case',
                ['foo' => 'bar'],
                'test case',
            ],
            'empty string' => [
                '',
                ['a' => 'b'],
                '',
            ],
            'special characters' => [
                'test@case#example',
                ['@' => 'at', '#' => 'hash'],
                'testatcasehashexample',
            ],
            'overlapping replacements' => [
                'hello',
                ['hell' => 'heaven', 'llo' => 'goodbye'],
                'heaveno',
            ],
        ];
    }
}
