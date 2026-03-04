<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Type;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Type\{ToBoolRule, ToStringRule, ToArrayRule};
use PHPUnit\Framework\TestCase;

final class TypeRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

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

    public function testToString(): void
    {
        $rule = new ToStringRule();
        $this->assertSame('42', $rule->sanitize(42, $this->ctx()));
        $this->assertSame('3.14', $rule->sanitize(3.14, $this->ctx()));
        $this->assertSame('1', $rule->sanitize(true, $this->ctx()));
        $this->assertSame('hello', $rule->sanitize('hello', $this->ctx()));
        $this->assertSame([1], $rule->sanitize([1], $this->ctx())); // non-scalar
    }

    public function testToArray(): void
    {
        $rule = new ToArrayRule();
        $this->assertSame([1, 2], $rule->sanitize([1, 2], $this->ctx()));
        $this->assertSame(['hello'], $rule->sanitize('hello', $this->ctx()));
        $this->assertSame([42], $rule->sanitize(42, $this->ctx()));
        $this->assertSame([], $rule->sanitize(null, $this->ctx()));
    }
}
