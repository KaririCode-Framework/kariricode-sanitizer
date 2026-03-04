<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Numeric;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Numeric\{ToIntRule, ToFloatRule, ClampRule, RoundRule};
use PHPUnit\Framework\TestCase;

final class NumericRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    public function testToInt(): void
    {
        $this->assertSame(42, (new ToIntRule())->sanitize('42', $this->ctx()));
        $this->assertSame(42, (new ToIntRule())->sanitize(42, $this->ctx()));
        $this->assertSame('abc', (new ToIntRule())->sanitize('abc', $this->ctx()));
    }

    public function testToFloat(): void
    {
        $this->assertSame(3.14, (new ToFloatRule())->sanitize('3.14', $this->ctx()));
        $this->assertSame(3.14, (new ToFloatRule())->sanitize(3.14, $this->ctx()));
        $this->assertSame('abc', (new ToFloatRule())->sanitize('abc', $this->ctx()));
    }

    public function testClamp(): void
    {
        $this->assertSame(5, (new ClampRule())->sanitize(3, $this->ctx(['min' => 5, 'max' => 10])));
        $this->assertSame(7, (new ClampRule())->sanitize(7, $this->ctx(['min' => 5, 'max' => 10])));
        $this->assertSame(10, (new ClampRule())->sanitize(15, $this->ctx(['min' => 5, 'max' => 10])));
        $this->assertSame('abc', (new ClampRule())->sanitize('abc', $this->ctx(['min' => 0])));
    }

    public function testRound(): void
    {
        $this->assertSame(3.14, (new RoundRule())->sanitize(3.14159, $this->ctx(['precision' => 2])));
    }

    public function testRoundCeil(): void
    {
        $this->assertSame(3.15, (new RoundRule())->sanitize(3.141, $this->ctx(['precision' => 2, 'mode' => 'ceil'])));
    }

    public function testRoundFloor(): void
    {
        $this->assertSame(3.14, (new RoundRule())->sanitize(3.149, $this->ctx(['precision' => 2, 'mode' => 'floor'])));
    }
}
