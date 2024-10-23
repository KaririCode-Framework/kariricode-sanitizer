<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\AlphanumericSanitizer;
use PHPUnit\Framework\TestCase;

final class AlphanumericSanitizerTest extends TestCase
{
    private AlphanumericSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new AlphanumericSanitizer();
    }

    /**
     * @dataProvider alphanumericDataProvider
     */
    public static function testAlphanumericSanitization(string $input, array $config, string $expected): void
    {
        $sanitizer = new AlphanumericSanitizer();
        $sanitizer->configure($config);
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function alphanumericDataProvider(): array
    {
        return [
            'basic alphanumeric' => [
                'Test123',
                [],
                'Test123',
            ],
            'with special characters' => [
                'Test@123!#$',
                [],
                'Test123',
            ],
            'allow spaces' => [
                'Test 123 Text',
                ['allowSpace' => true],
                'Test 123 Text',
            ],
            'allow underscore' => [
                'test_123_text',
                ['allowUnderscore' => true],
                'test_123_text',
            ],
            'allow dash' => [
                'test-123-text',
                ['allowDash' => true],
                'test-123-text',
            ],
            'allow dot' => [
                'test.123.text',
                ['allowDot' => true],
                'test.123.text',
            ],
            'multiple allowed chars' => [
                'test.123-text_example 456',
                [
                    'allowSpace' => true,
                    'allowUnderscore' => true,
                    'allowDash' => true,
                    'allowDot' => true,
                ],
                'test.123-text_example 456',
            ],
            'lowercase conversion' => [
                'TEST123',
                ['preserveCase' => false],
                'test123',
            ],
            'empty string' => [
                '',
                [],
                '',
            ],
            'only special chars' => [
                '@#$%^&',
                [],
                '',
            ],
            'mixed case with spaces' => [
                'Test USER 123',
                ['allowSpace' => true],
                'Test USER 123',
            ],
            'mixed special chars' => [
                'test@user.name-123_example',
                ['allowDot' => true, 'allowDash' => true],
                'testuser.name-123example',
            ],
        ];
    }
}
