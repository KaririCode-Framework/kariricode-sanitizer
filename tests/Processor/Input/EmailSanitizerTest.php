<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\EmailSanitizer;
use PHPUnit\Framework\TestCase;

final class EmailSanitizerTest extends TestCase
{
    private EmailSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new EmailSanitizer();
    }

    /**
     * @dataProvider emailDataProvider
     */
    public static function testEmailSanitization(string $input, string $expected): void
    {
        $sanitizer = new EmailSanitizer();
        self::assertSame($expected, $sanitizer->process($input));
    }

    public function testCustomTypoReplacements(): void
    {
        $this->sanitizer->configure([
            'typoReplacements' => [
                'at' => '@',
                '[dot]' => '.',
            ],
        ]);

        $input = 'walmir[dot]silvaatexample[dot]com';
        $expected = 'walmir.silva@example.com';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testCustomDomainReplacements(): void
    {
        $this->sanitizer->configure([
            'domainReplacements' => [
                'kariricode.com' => ['kariri-code.com', 'kariricode.com.br'],
            ],
        ]);

        $input = 'walmir@kariri-code.com';
        $expected = 'walmir@kariricode.com';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testHandleNonStringInput(): void
    {
        $this->expectException(SanitizationException::class);
        $this->sanitizer->process(123);
    }

    public static function emailDataProvider(): array
    {
        return [
            'basic email' => [
                'Walmir.Silva@Example.com',
                'walmir.silva@example.com',
            ],
            'with spaces' => [
                '  walmir.silva@example.com  ',
                'walmir.silva@example.com',
            ],
            'common typos' => [
                'walmir,,silva@gmial.com',
                'walmir.silva@gmail.com',
            ],
            'multiple dots' => [
                'walmir...silva@example.com',
                'walmir.silva@example.com',
            ],
            'mailto prefix' => [
                'mailto:walmir.silva@example.com',
                'walmir.silva@example.com',
            ],
            'domain typos' => [
                'walmir@gmaill.com',
                'walmir@gmail.com',
            ],
            'mixed case' => [
                'WALMIR.SILVA@EXAMPLE.COM',
                'walmir.silva@example.com',
            ],
        ];
    }
}
