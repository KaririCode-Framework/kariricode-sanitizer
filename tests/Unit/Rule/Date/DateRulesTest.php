<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Date;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Date\{NormalizeDateRule, TimestampToDateRule};
use PHPUnit\Framework\TestCase;

final class DateRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    public function testNormalizeDateBrToIso(): void
    {
        $result = (new NormalizeDateRule())->sanitize('28/02/2025', $this->ctx(['from' => 'd/m/Y', 'to' => 'Y-m-d']));
        $this->assertSame('2025-02-28', $result);
    }

    public function testNormalizeDateInvalidReturnsOriginal(): void
    {
        $this->assertSame('invalid', (new NormalizeDateRule())->sanitize('invalid', $this->ctx()));
    }

    public function testNormalizeDateEmptyReturnsOriginal(): void
    {
        $this->assertSame('', (new NormalizeDateRule())->sanitize('', $this->ctx()));
    }

    public function testTimestampToDate(): void
    {
        $result = (new TimestampToDateRule())->sanitize(1740700800, $this->ctx(['format' => 'Y-m-d', 'timezone' => 'UTC']));
        $this->assertSame('2025-02-28', $result);
    }

    public function testTimestampToDateNonNumeric(): void
    {
        $this->assertSame('abc', (new TimestampToDateRule())->sanitize('abc', $this->ctx()));
    }
}
