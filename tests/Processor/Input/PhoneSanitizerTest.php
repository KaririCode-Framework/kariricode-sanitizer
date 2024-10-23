<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\PhoneSanitizer;
use PHPUnit\Framework\TestCase;

final class PhoneSanitizerTest extends TestCase
{
    private PhoneSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new PhoneSanitizer();
    }

    /**
     * @dataProvider phoneDataProvider
     */
    public static function testPhoneSanitization(string $input, array $config, string $expected): void
    {
        $sanitizer = new PhoneSanitizer();
        $sanitizer->configure($config);
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function phoneDataProvider(): array
    {
        return [
            'basic number without format' => [
                '(11) 99999-9999',
                [],
                '11999999999',
            ],
            'mixed characters' => [
                'Tel: +55 (11) 99999-9999',
                [],
                '5511999999999',
            ],
            'with special characters' => [
                '11.99999-9999',
                [],
                '11999999999',
            ],
            'with optional formatting' => [
                '11999999999',
                [
                    'applyFormat' => true,
                    'format' => '(##) #####-####',
                ],
                '(11) 99999-9999',
            ],
            'international with format' => [
                '5511999999999',
                [
                    'applyFormat' => true,
                    'format' => '+## (##) #####-####',
                ],
                '+55 (11) 99999-9999',
            ],
            'partial number' => [
                '11999',
                [
                    'applyFormat' => true,
                    'format' => '(##) #####-####',
                ],
                '11999',
            ],
            'custom format' => [
                '11999999999',
                [
                    'applyFormat' => true,
                    'format' => '## # ####-####',
                ],
                '11 9 9999-9999',
            ],
            'empty string' => [
                '',
                [],
                '',
            ],
            'only non-numeric' => [
                '(abc) def-ghij',
                [],
                '',
            ],
            'with custom placeholder' => [
                '11999999999',
                [
                    'applyFormat' => true,
                    'format' => '(**)_*****-****',
                    'placeholder' => '*',
                ],
                '(11)_99999-9999',
            ],
        ];
    }
}
