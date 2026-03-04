<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Numeric;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Numeric\ClampRule;
use KaririCode\Sanitizer\Rule\Numeric\RoundRule;
use KaririCode\Sanitizer\Rule\Numeric\ToFloatRule;
use KaririCode\Sanitizer\Rule\Numeric\ToIntRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ToIntRule::class)]
#[CoversClass(ToFloatRule::class)]
#[CoversClass(ClampRule::class)]
#[CoversClass(RoundRule::class)]
final class NumericRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    #[Test]
    public function testToInt(): void
    {
        $this->assertSame(42, new ToIntRule()->sanitize('42', $this->ctx()));
        $this->assertSame(42, new ToIntRule()->sanitize(42, $this->ctx()));
        $this->assertSame('abc', new ToIntRule()->sanitize('abc', $this->ctx()));
    }

    #[Test]
    public function testToFloat(): void
    {
        $this->assertSame(3.14, new ToFloatRule()->sanitize('3.14', $this->ctx()));
        $this->assertSame(3.14, new ToFloatRule()->sanitize(3.14, $this->ctx()));
        $this->assertSame('abc', new ToFloatRule()->sanitize('abc', $this->ctx()));
    }

    #[Test]
    public function testClamp(): void
    {
        $this->assertSame(5, new ClampRule()->sanitize(3, $this->ctx(['min' => 5, 'max' => 10])));
        $this->assertSame(7, new ClampRule()->sanitize(7, $this->ctx(['min' => 5, 'max' => 10])));
        $this->assertSame(10, new ClampRule()->sanitize(15, $this->ctx(['min' => 5, 'max' => 10])));
        $this->assertSame('abc', new ClampRule()->sanitize('abc', $this->ctx(['min' => 0])));
    }

    #[Test]
    public function testRound(): void
    {
        $this->assertSame(3.14, new RoundRule()->sanitize(3.14159, $this->ctx(['precision' => 2])));
    }

    #[Test]
    public function testRoundCeil(): void
    {
        $this->assertSame(3.15, new RoundRule()->sanitize(3.141, $this->ctx(['precision' => 2, 'mode' => 'ceil'])));
    }

    #[Test]
    public function testRoundFloor(): void
    {
        $this->assertSame(3.14, new RoundRule()->sanitize(3.149, $this->ctx(['precision' => 2, 'mode' => 'floor'])));
    }

    #[Test]
    public function testRoundNonNumeric(): void
    {
        $this->assertSame('abc', new RoundRule()->sanitize('abc', $this->ctx()));
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testToIntRuleName(): void
    {
        $this->assertSame('numeric.to_int', new ToIntRule()->getName());
    }

    #[Test]
    public function testToFloatRuleName(): void
    {
        $this->assertSame('numeric.to_float', new ToFloatRule()->getName());
    }

    #[Test]
    public function testClampRuleName(): void
    {
        $this->assertSame('numeric.clamp', new ClampRule()->getName());
    }

    #[Test]
    public function testRoundRuleName(): void
    {
        $this->assertSame('numeric.round', new RoundRule()->getName());
    }
}
