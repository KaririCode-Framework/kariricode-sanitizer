<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\CaseTransformerTrait;
use PHPUnit\Framework\TestCase;

final class CaseTransformerTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use CaseTransformerTrait;

            public function callToLowerCase(string $input): string
            {
                return $this->toLowerCase($input);
            }

            public function callToUpperCase(string $input): string
            {
                return $this->toUpperCase($input);
            }

            public function callToCamelCase(string $input): string
            {
                return $this->toCamelCase($input);
            }
        };
    }

    /**
     * @dataProvider toLowerCaseProvider
     */
    public function testToLowerCase(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callToLowerCase($input));
    }

    /**
     * @dataProvider toUpperCaseProvider
     */
    public function testToUpperCase(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callToUpperCase($input));
    }

    /**
     * @dataProvider toCamelCaseProvider
     */
    public function testToCamelCase(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callToCamelCase($input));
    }

    public static function toLowerCaseProvider(): array
    {
        return [
            'mixed case' => ['HelloWorld', 'helloworld'],
            'already lowercase' => ['hello', 'hello'],
            'all uppercase' => ['HELLO', 'hello'],
            'with numbers' => ['Hello123World', 'hello123world'],
            'with special chars' => ['Hello@World', 'hello@world'],
            'empty string' => ['', ''],
        ];
    }

    public static function toUpperCaseProvider(): array
    {
        return [
            'mixed case' => ['HelloWorld', 'HELLOWORLD'],
            'already uppercase' => ['HELLO', 'HELLO'],
            'all lowercase' => ['hello', 'HELLO'],
            'with numbers' => ['Hello123World', 'HELLO123WORLD'],
            'with special chars' => ['Hello@World', 'HELLO@WORLD'],
            'empty string' => ['', ''],
        ];
    }

    public static function toCamelCaseProvider(): array
    {
        return [
            'simple underscored' => ['hello_world', 'helloWorld'],
            'multiple underscores' => ['hello_beautiful_world', 'helloBeautifulWorld'],
            'already camel case' => ['helloWorld', 'helloWorld'],
            'uppercase' => ['HELLO_WORLD', 'helloWorld'],
            'with numbers' => ['hello_123_world', 'hello123World'],
            'empty string' => ['', ''],
            'single word' => ['hello', 'hello'],
        ];
    }
}
