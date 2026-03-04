<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Filter;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Filter\{DigitsOnlyRule, AlphaOnlyRule, AlphanumericOnlyRule, EmailFilterRule};
use PHPUnit\Framework\TestCase;

final class FilterRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    public function testDigitsOnly(): void
    {
        $this->assertSame('12345678901', (new DigitsOnlyRule())->sanitize('123.456.789-01', $this->ctx()));
    }

    public function testAlphaOnly(): void
    {
        $this->assertSame('SãoPaulo', (new AlphaOnlyRule())->sanitize('São Paulo 123!', $this->ctx()));
    }

    public function testAlphanumericOnly(): void
    {
        $this->assertSame('São123', (new AlphanumericOnlyRule())->sanitize('São-123!', $this->ctx()));
    }

    public function testEmailFilter(): void
    {
        $this->assertSame('user@test.com', (new EmailFilterRule())->sanitize('  User@Test.COM  ', $this->ctx()));
    }

    public function testDigitsOnlyNonString(): void
    {
        $this->assertSame(42, (new DigitsOnlyRule())->sanitize(42, $this->ctx()));
    }
}
