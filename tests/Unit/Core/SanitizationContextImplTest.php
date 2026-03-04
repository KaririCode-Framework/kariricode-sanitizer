<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Core;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use PHPUnit\Framework\TestCase;

final class SanitizationContextImplTest extends TestCase
{
    public function testCreateReturnsEmptyFieldAndEmptyParams(): void
    {
        $ctx = SanitizationContextImpl::create(['a' => 1]);

        $this->assertSame('', $ctx->getFieldName());
        $this->assertSame(['a' => 1], $ctx->getRootData());
        $this->assertSame([], $ctx->getParameters());
    }

    public function testWithFieldReturnsNewInstance(): void
    {
        $ctx = SanitizationContextImpl::create([]);
        $ctx2 = $ctx->withField('email');

        $this->assertSame('', $ctx->getFieldName());
        $this->assertSame('email', $ctx2->getFieldName());
        $this->assertNotSame($ctx, $ctx2);
    }

    public function testWithParametersMerges(): void
    {
        $ctx = SanitizationContextImpl::create([])
            ->withParameters(['a' => 1])
            ->withParameters(['b' => 2]);

        $this->assertSame(1, $ctx->getParameter('a'));
        $this->assertSame(2, $ctx->getParameter('b'));
        $this->assertNull($ctx->getParameter('c'));
        $this->assertSame('default', $ctx->getParameter('c', 'default'));
    }
}
