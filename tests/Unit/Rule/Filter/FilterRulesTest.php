<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Filter;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Filter\AlphanumericOnlyRule;
use KaririCode\Sanitizer\Rule\Filter\AlphaOnlyRule;
use KaririCode\Sanitizer\Rule\Filter\DigitsOnlyRule;
use KaririCode\Sanitizer\Rule\Filter\EmailFilterRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DigitsOnlyRule::class)]
#[CoversClass(AlphaOnlyRule::class)]
#[CoversClass(AlphanumericOnlyRule::class)]
#[CoversClass(EmailFilterRule::class)]
final class FilterRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    #[Test]
    public function testDigitsOnly(): void
    {
        $this->assertSame('12345678901', new DigitsOnlyRule()->sanitize('123.456.789-01', $this->ctx()));
    }

    #[Test]
    public function testAlphaOnly(): void
    {
        $this->assertSame('SãoPaulo', new AlphaOnlyRule()->sanitize('São Paulo 123!', $this->ctx()));
    }

    #[Test]
    public function testAlphanumericOnly(): void
    {
        $this->assertSame('São123', new AlphanumericOnlyRule()->sanitize('São-123!', $this->ctx()));
    }

    #[Test]
    public function testEmailFilter(): void
    {
        $this->assertSame('user@test.com', new EmailFilterRule()->sanitize('  User@Test.COM  ', $this->ctx()));
    }

    #[Test]
    public function testDigitsOnlyNonString(): void
    {
        $this->assertSame(42, new DigitsOnlyRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testAlphaOnlyNonString(): void
    {
        $this->assertSame(42, new AlphaOnlyRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testAlphanumericOnlyNonString(): void
    {
        $this->assertSame(42, new AlphanumericOnlyRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testEmailFilterNonString(): void
    {
        $this->assertSame(42, new EmailFilterRule()->sanitize(42, $this->ctx()));
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testDigitsOnlyRuleName(): void
    {
        $this->assertSame('filter.digits_only', new DigitsOnlyRule()->getName());
    }

    #[Test]
    public function testAlphaOnlyRuleName(): void
    {
        $this->assertSame('filter.alpha_only', new AlphaOnlyRule()->getName());
    }

    #[Test]
    public function testAlphanumericOnlyRuleName(): void
    {
        $this->assertSame('filter.alphanumeric_only', new AlphanumericOnlyRule()->getName());
    }

    #[Test]
    public function testEmailFilterRuleName(): void
    {
        $this->assertSame('filter.email', new EmailFilterRule()->getName());
    }
}
