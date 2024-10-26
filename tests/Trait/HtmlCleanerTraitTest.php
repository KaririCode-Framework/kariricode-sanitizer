<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\HtmlCleanerTrait;
use PHPUnit\Framework\TestCase;

final class HtmlCleanerTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use HtmlCleanerTrait;

            public function callRemoveScripts(string $input): string
            {
                return $this->removeScripts($input);
            }

            public function callRemoveComments(string $input): string
            {
                return $this->removeComments($input);
            }

            public function callRemoveStyle(string $input): string
            {
                return $this->removeStyle($input);
            }
        };
    }

    /**
     * @dataProvider removeScriptsProvider
     */
    public function testRemoveScripts(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callRemoveScripts($input));
    }

    /**
     * @dataProvider removeCommentsProvider
     */
    public function testRemoveComments(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callRemoveComments($input));
    }

    /**
     * @dataProvider removeStyleProvider
     */
    public function testRemoveStyle(string $input, string $expected): void
    {
        $this->assertSame($expected, $this->traitObject->callRemoveStyle($input));
    }

    public static function removeScriptsProvider(): array
    {
        return [
            'basic script' => [
                '<p>Text</p><script>alert("test");</script>',
                '<p>Text</p>',
            ],
            'script with attributes' => [
                '<script type="text/javascript">console.log("test");</script>',
                '',
            ],
            'inline event handler' => [
                '<a onclick="alert(\'test\')">Click me</a>',
                '<a >Click me</a>',
            ],
            'multiple scripts' => [
                '<script>test1();</script><p>Text</p><script>test2();</script>',
                '<p>Text</p>',
            ],
            'no scripts' => [
                '<p>Clean text</p>',
                '<p>Clean text</p>',
            ],
        ];
    }

    public static function removeCommentsProvider(): array
    {
        return [
            'basic comment' => [
                '<p>Text</p><!-- Comment -->',
                '<p>Text</p>',
            ],
            'multiple comments' => [
                '<!-- Comment 1 --><p>Text</p><!-- Comment 2 -->',
                '<p>Text</p>',
            ],
            'multiline comment' => [
                "<p>Text</p><!-- Multi\nline\ncomment -->",
                '<p>Text</p>',
            ],
            'no comments' => [
                '<p>Clean text</p>',
                '<p>Clean text</p>',
            ],
            'nested comments' => [
                '<!-- Outer <!-- Inner --> Comment -->',
                '',
            ],
        ];
    }

    public static function removeStyleProvider(): array
    {
        return [
            'basic style' => [
                '<style>body { color: red; }</style>',
                '',
            ],
            'style with attributes' => [
                '<style type="text/css">p { color: blue; }</style>',
                '',
            ],
            'multiple styles' => [
                '<style>body { margin: 0; }</style><p>Text</p><style>.red { color: red; }</style>',
                '<p>Text</p>',
            ],
            'no styles' => [
                '<p>Clean text</p>',
                '<p>Clean text</p>',
            ],
        ];
    }
}
