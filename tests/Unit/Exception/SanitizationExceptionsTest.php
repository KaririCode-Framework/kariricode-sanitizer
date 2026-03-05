<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Exception;

use KaririCode\Sanitizer\Exception\InvalidRuleException;
use KaririCode\Sanitizer\Exception\SanitizationException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

// NOTE: #[CoversClass] is intentionally omitted for exception classes.
// In PHPUnit 12 + pcov, classes that directly extend \RuntimeException
// (a native PHP class) are reported as "not a valid target for code coverage",
// which triggers PHPUnit warnings that — with failOnWarning="true" — exit as code 1.
// Coverage for these classes is achieved transitively via SanitizerEngineTest and others.
final class SanitizationExceptionsTest extends TestCase
{
    #[Test]
    public function testInvalidRuleExceptionDuplicateAlias(): void
    {
        $e = InvalidRuleException::duplicateAlias('trim');

        $this->assertInstanceOf(InvalidRuleException::class, $e);
        $this->assertSame("Sanitization rule alias 'trim' is already registered.", $e->getMessage());
    }

    #[Test]
    public function testInvalidRuleExceptionUnknownAlias(): void
    {
        $e = InvalidRuleException::unknownAlias('unknown');

        $this->assertInstanceOf(InvalidRuleException::class, $e);
        $this->assertSame("Sanitization rule alias 'unknown' is not registered.", $e->getMessage());
    }

    #[Test]
    public function testSanitizationExceptionEngineError(): void
    {
        $e = SanitizationException::engineError('something went wrong');

        $this->assertInstanceOf(SanitizationException::class, $e);
        $this->assertSame('Sanitization engine error: something went wrong', $e->getMessage());
        $this->assertNull($e->getPrevious());
    }

    #[Test]
    public function testSanitizationExceptionEngineErrorWithPrevious(): void
    {
        $previous = new \RuntimeException('root cause');
        $e = SanitizationException::engineError('wrapped', $previous);

        $this->assertSame($previous, $e->getPrevious());
    }
}
