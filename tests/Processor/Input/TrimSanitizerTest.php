<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use PHPUnit\Framework\TestCase;

final class TrimSanitizerTest extends TestCase
{
    private TrimSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new TrimSanitizer();
    }

    /**
     * @dataProvider trimDataProvider
     */
    public static function testBasicTrim(string $input, array $config, string $expected): void
    {
        $sanitizer = new TrimSanitizer();
        $sanitizer->configure($config);
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testCustomCharacterMask(): void
    {
        $this->sanitizer->configure(['characterMask' => 'xyz']);
        $this->assertSame('123', $this->sanitizer->process('xyz123xxxyz'));

        $this->sanitizer->configure(['characterMask' => 'ab']);
        $this->assertSame('123', $this->sanitizer->process('aabb123bbaa'));

        $this->sanitizer->configure(['characterMask' => '_']);
        $this->assertSame('abc-123', $this->sanitizer->process('__abc-123__'));
    }

    public function testSelectiveTrimming(): void
    {
        // Test left trim only
        $sanitizer = new TrimSanitizer();
        $sanitizer->configure(['trimRight' => false]);
        $this->assertSame('test  ', $sanitizer->process('  test  '));

        // Test right trim only
        $sanitizer = new TrimSanitizer();
        $sanitizer->configure(['trimLeft' => false]);
        $this->assertSame('  test', $sanitizer->process('  test  '));

        // Test no trimming
        $sanitizer = new TrimSanitizer();
        $sanitizer->configure(['trimLeft' => false, 'trimRight' => false]);
        $this->assertSame('  test  ', $sanitizer->process('  test  '));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function trimDataProvider(): array
    {
        return [
            'basic spaces' => [
                '  test  ',
                [],
                'test',
            ],
            'tabs and newlines' => [
                "\t\ttest\n\n",
                [],
                'test',
            ],
            'mixed whitespace' => [
                " \t\n\r\0\x0Btest \t\n\r\0\x0B",
                [],
                'test',
            ],
            'custom mask' => [
                '...test...',
                ['characterMask' => '.'],
                'test',
            ],
            'left trim only' => [
                '  test  ',
                ['trimRight' => false],
                'test  ',
            ],
            'right trim only' => [
                '  test  ',
                ['trimLeft' => false],
                '  test',
            ],
            'no trim' => [
                '  test  ',
                ['trimLeft' => false, 'trimRight' => false],
                '  test  ',
            ],
            'preserve characters in middle' => [
                'xyz123xxxyz',
                ['characterMask' => 'xyz'],
                '123',
            ],
        ];
    }
}
