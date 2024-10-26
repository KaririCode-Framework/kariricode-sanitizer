<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\CharacterFilterTrait;
use PHPUnit\Framework\TestCase;

final class CharacterFilterTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use CharacterFilterTrait;

            public function callFilterAllowedCharacters(string $input, string $allowed): string
            {
                return $this->filterAllowedCharacters($input, $allowed);
            }

            public function callKeepOnlyAlphanumeric(string $input, array $additionalChars = []): string
            {
                return $this->keepOnlyAlphanumeric($input, $additionalChars);
            }
        };
    }

    /**
     * @dataProvider filterAllowedCharactersProvider
     */
    public function testFilterAllowedCharacters(string $input, string $allowed, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callFilterAllowedCharacters($input, $allowed));
    }

    /**
     * @dataProvider keepOnlyAlphanumericProvider
     */
    public function testKeepOnlyAlphanumeric(string $input, array $additionalChars, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callKeepOnlyAlphanumeric($input, $additionalChars));
    }

    public static function filterAllowedCharactersProvider(): array
    {
        return [
            'basic filtering' => ['hello123!@#', 'a-z', 'hello'],
            'numbers only' => ['hello123!@#', '0-9', '123'],
            'mixed allowed chars' => ['hello123!@#', 'a-z0-9', 'hello123'],
            'special chars' => ['hello@world!', '@!', '@!'],
            'empty string' => ['', 'a-z', ''],
            'no allowed chars' => ['hello123', 'x-z', ''],
            'with spaces' => ['hello world', 'a-z ', 'hello world'],
        ];
    }

    public static function keepOnlyAlphanumericProvider(): array
    {
        return [
            'basic alphanumeric' => ['hello123!@#', [], 'hello123'],
            'with dash' => ['hello-123', ['-'], 'hello-123'],
            'with multiple chars' => ['hello@world!123', ['@', '!'], 'hello@world!123'],
            'empty string' => ['', [], ''],
            'only special chars' => ['!@#$%', [], ''],
            'with spaces' => ['hello world', [' '], 'hello world'],
        ];
    }
}
