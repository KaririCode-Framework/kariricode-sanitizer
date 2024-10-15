<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Security;

use KaririCode\Sanitizer\Processor\Security\SqlInjectionSanitizer;
use PHPUnit\Framework\TestCase;

final class SqlInjectionSanitizerTest extends TestCase
{
    private SqlInjectionSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new SqlInjectionSanitizer();
    }

    public function testProcessWithSafeInput(): void
    {
        $input = 'Safe input without SQL keywords';
        $expected = 'Safe input without SQL keywords';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testProcessWithSqlInjectionAttempt(): void
    {
        $input = "SELECT * FROM users WHERE name='admin'--";
        $expected = "SELECT * FROM users WHERE name=\\'admin\\'";

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testProcessWithCustomEscapeMap(): void
    {
        $customSanitizer = new SqlInjectionSanitizer();
        $customSanitizer->configure([
            'escapeMap' => ["'" => "''", '\\' => '\\\\'],
        ]);

        $input = "O'Reilly";
        $expected = "O''Reilly";

        $this->assertSame($expected, $customSanitizer->process($input));
    }

    public function testProcessWithEscapedCharacters(): void
    {
        $input = "This contains a null byte: \x00 and a quote: '";
        $expected = "This contains a null byte: \\0 and a quote: \\'";

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testProcessWithMultiLineComment(): void
    {
        $input = 'SELECT * FROM users /* hidden comment */ WHERE id = 1;';
        $expected = 'SELECT * FROM users WHERE id = 1';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testProcessWithSemicolons(): void
    {
        $input = 'DROP TABLE users;';
        $expected = 'DROP TABLE users';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testProcessWithNormalizedWhitespace(): void
    {
        $input = 'SELECT    *    FROM   users';
        $expected = 'SELECT * FROM users';

        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testRemoveSuspiciousPatterns(): void
    {
        $reflection = new \ReflectionClass(SqlInjectionSanitizer::class);
        $method = $reflection->getMethod('removeSuspiciousPatterns');
        $method->setAccessible(true);

        $input = 'SELECT * FROM users WHERE id = 1; -- malicious comment';
        $expected = 'SELECT * FROM users WHERE id = 1 ';

        $this->assertSame($expected, $method->invoke($this->sanitizer, $input));
    }

    public function testEscapeString(): void
    {
        $reflection = new \ReflectionClass(SqlInjectionSanitizer::class);
        $method = $reflection->getMethod('escapeString');
        $method->setAccessible(true);

        $input = "This contains a null byte: \x00 and a quote: '";
        $expected = "This contains a null byte: \\0 and a quote: \\'";

        $this->assertSame($expected, $method->invoke($this->sanitizer, $input));
    }
}
