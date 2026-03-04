<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Date;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Date\NormalizeDateRule;
use KaririCode\Sanitizer\Rule\Date\TimestampToDateRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(NormalizeDateRule::class)]
#[CoversClass(TimestampToDateRule::class)]
final class DateRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    #[Test]
    public function testNormalizeDateBrToIso(): void
    {
        $result = new NormalizeDateRule()->sanitize('28/02/2025', $this->ctx(['from' => 'd/m/Y', 'to' => 'Y-m-d']));
        $this->assertSame('2025-02-28', $result);
    }

    #[Test]
    public function testNormalizeDateInvalidReturnsOriginal(): void
    {
        $this->assertSame('invalid', new NormalizeDateRule()->sanitize('invalid', $this->ctx()));
    }

    #[Test]
    public function testNormalizeDateEmptyReturnsOriginal(): void
    {
        $this->assertSame('', new NormalizeDateRule()->sanitize('', $this->ctx()));
    }

    #[Test]
    public function testTimestampToDate(): void
    {
        $result = new TimestampToDateRule()->sanitize(1740700800, $this->ctx(['format' => 'Y-m-d', 'timezone' => 'UTC']));
        $this->assertSame('2025-02-28', $result);
    }

    #[Test]
    public function testTimestampToDateNonNumeric(): void
    {
        $this->assertSame('abc', new TimestampToDateRule()->sanitize('abc', $this->ctx()));
    }

    #[Test]
    public function testTimestampToDateInvalidTimezoneReturnOriginal(): void
    {
        // An invalid timezone string triggers the catch branch which returns the original value.
        $result = new TimestampToDateRule()->sanitize(1740700800, $this->ctx(['format' => 'Y-m-d', 'timezone' => 'Invalid/Zone']));
        $this->assertSame(1740700800, $result);
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testNormalizeDateRuleName(): void
    {
        $this->assertSame('date.normalize', new NormalizeDateRule()->getName());
    }

    #[Test]
    public function testTimestampToDateRuleName(): void
    {
        $this->assertSame('date.timestamp_to_date', new TimestampToDateRule()->getName());
    }
}
