<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Core;

use KaririCode\Sanitizer\Core\InMemoryRuleRegistry;
use KaririCode\Sanitizer\Exception\InvalidRuleException;
use KaririCode\Sanitizer\Rule\String\TrimRule;
use PHPUnit\Framework\TestCase;

final class InMemoryRuleRegistryTest extends TestCase
{
    public function testRegisterAndResolve(): void
    {
        $registry = new InMemoryRuleRegistry();
        $rule = new TrimRule();
        $registry->register('trim', $rule);

        $this->assertTrue($registry->has('trim'));
        $this->assertFalse($registry->has('unknown'));
        $this->assertSame($rule, $registry->resolve('trim'));
        $this->assertSame(['trim'], $registry->aliases());
    }

    public function testDuplicateAliasThrows(): void
    {
        $registry = new InMemoryRuleRegistry();
        $registry->register('trim', new TrimRule());

        $this->expectException(InvalidRuleException::class);
        $registry->register('trim', new TrimRule());
    }

    public function testUnknownAliasThrows(): void
    {
        $registry = new InMemoryRuleRegistry();

        $this->expectException(InvalidRuleException::class);
        $registry->resolve('unknown');
    }
}
