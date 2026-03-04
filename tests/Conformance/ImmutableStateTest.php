<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Conformance;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use PHPUnit\Framework\TestCase;

/**
 * ARFA 1.3 P1: Immutable State — context.withField() returns new instance.
 */
final class ImmutableStateTest extends TestCase
{
    public function testContextWithFieldReturnsNewInstance(): void
    {
        $ctx = SanitizationContextImpl::create(['a' => 1]);
        $ctx2 = $ctx->withField('email');

        $this->assertNotSame($ctx, $ctx2);
        $this->assertSame('', $ctx->getFieldName());
        $this->assertSame('email', $ctx2->getFieldName());
    }

    public function testContextWithParametersReturnsNewInstance(): void
    {
        $ctx = SanitizationContextImpl::create([]);
        $ctx2 = $ctx->withParameters(['max' => 10]);

        $this->assertNotSame($ctx, $ctx2);
        $this->assertSame([], $ctx->getParameters());
        $this->assertSame(['max' => 10], $ctx2->getParameters());
    }
}
