<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Type;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Type\ToArrayRule;
use KaririCode\Sanitizer\Rule\Type\ToBoolRule;
use KaririCode\Sanitizer\Rule\Type\ToStringRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ToBoolRule::class)]
#[CoversClass(ToStringRule::class)]
#[CoversClass(ToArrayRule::class)]
final class TypeRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    #[Test]
    public function testToBool(): void
    {
        $rule = new ToBoolRule();
        $this->assertTrue($rule->sanitize('true', $this->ctx()));
        $this->assertTrue($rule->sanitize('yes', $this->ctx()));
        $this->assertTrue($rule->sanitize('1', $this->ctx()));
        $this->assertTrue($rule->sanitize('on', $this->ctx()));
        $this->assertFalse($rule->sanitize('false', $this->ctx()));
        $this->assertFalse($rule->sanitize('no', $this->ctx()));
        $this->assertFalse($rule->sanitize('0', $this->ctx()));
        $this->assertFalse($rule->sanitize('off', $this->ctx()));
        $this->assertFalse($rule->sanitize('', $this->ctx()));
        $this->assertSame('maybe', $rule->sanitize('maybe', $this->ctx()));
        $this->assertTrue($rule->sanitize(true, $this->ctx()));
    }

    #[Test]
    public function testToBoolNumericValue(): void
    {
        $rule = new ToBoolRule();
        $this->assertTrue($rule->sanitize(1, $this->ctx()));
        $this->assertFalse($rule->sanitize(0, $this->ctx()));
    }

    #[Test]
    public function testToBoolNonBoolNonStringNonNumericPassthrough(): void
    {
        // Covers the final `return $value` branch (line 34) when value is none of bool/string/numeric.
        $rule = new ToBoolRule();
        $arr = ['key' => 'value'];
        $this->assertSame($arr, $rule->sanitize($arr, $this->ctx()));
    }

    #[Test]
    public function testToString(): void
    {
        $rule = new ToStringRule();
        $this->assertSame('42', $rule->sanitize(42, $this->ctx()));
        $this->assertSame('3.14', $rule->sanitize(3.14, $this->ctx()));
        $this->assertSame('1', $rule->sanitize(true, $this->ctx()));
        $this->assertSame('hello', $rule->sanitize('hello', $this->ctx()));
        $this->assertSame([1], $rule->sanitize([1], $this->ctx())); // non-scalar passthrough
    }

    #[Test]
    public function testToArray(): void
    {
        $rule = new ToArrayRule();
        $this->assertSame([1, 2], $rule->sanitize([1, 2], $this->ctx()));
        $this->assertSame(['hello'], $rule->sanitize('hello', $this->ctx()));
        $this->assertSame([42], $rule->sanitize(42, $this->ctx()));
        $this->assertSame([], $rule->sanitize(null, $this->ctx()));
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testToBoolRuleName(): void
    {
        $this->assertSame('type.to_bool', new ToBoolRule()->getName());
    }

    #[Test]
    public function testToStringRuleName(): void
    {
        $this->assertSame('type.to_string', new ToStringRule()->getName());
    }

    #[Test]
    public function testToArrayRuleName(): void
    {
        $this->assertSame('type.to_array', new ToArrayRule()->getName());
    }
}
