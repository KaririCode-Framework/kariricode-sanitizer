<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Brazilian;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Brazilian\FormatCepRule;
use KaririCode\Sanitizer\Rule\Brazilian\FormatCnpjRule;
use KaririCode\Sanitizer\Rule\Brazilian\FormatCpfRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(FormatCpfRule::class)]
#[CoversClass(FormatCnpjRule::class)]
#[CoversClass(FormatCepRule::class)]
final class BrazilianRulesTest extends TestCase
{
    private function ctx(): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test');
    }

    // ── CPF ───────────────────────────────────────────────────────

    #[Test]
    public function testFormatCpfFromDigits(): void
    {
        $this->assertSame('529.982.247-25', new FormatCpfRule()->sanitize('52998224725', $this->ctx()));
    }

    #[Test]
    public function testFormatCpfFromFormatted(): void
    {
        $this->assertSame('529.982.247-25', new FormatCpfRule()->sanitize('529.982.247-25', $this->ctx()));
    }

    #[Test]
    public function testFormatCpfInvalidLength(): void
    {
        $this->assertSame('1234', new FormatCpfRule()->sanitize('1234', $this->ctx()));
    }

    // ── CNPJ ──────────────────────────────────────────────────────

    #[Test]
    public function testFormatCnpjFromDigits(): void
    {
        $this->assertSame('11.222.333/0001-81', new FormatCnpjRule()->sanitize('11222333000181', $this->ctx()));
    }

    #[Test]
    public function testFormatCnpjFromFormatted(): void
    {
        $this->assertSame('11.222.333/0001-81', new FormatCnpjRule()->sanitize('11.222.333/0001-81', $this->ctx()));
    }

    #[Test]
    public function testFormatCnpjInvalidLength(): void
    {
        $this->assertSame('123', new FormatCnpjRule()->sanitize('123', $this->ctx()));
    }

    // ── CEP ───────────────────────────────────────────────────────

    #[Test]
    public function testFormatCepFromDigits(): void
    {
        $this->assertSame('63100-000', new FormatCepRule()->sanitize('63100000', $this->ctx()));
    }

    #[Test]
    public function testFormatCepFromFormatted(): void
    {
        $this->assertSame('63100-000', new FormatCepRule()->sanitize('63100-000', $this->ctx()));
    }

    #[Test]
    public function testFormatCepInvalidLength(): void
    {
        $this->assertSame('123', new FormatCepRule()->sanitize('123', $this->ctx()));
    }

    // ── Non-string passthrough ────────────────────────────────────

    #[Test]
    public function testFormatRulesHandleNonString(): void
    {
        $ctx = $this->ctx();
        $this->assertSame(42, new FormatCpfRule()->sanitize(42, $ctx));
        $this->assertSame(null, new FormatCnpjRule()->sanitize(null, $ctx));
        $this->assertSame([], new FormatCepRule()->sanitize([], $ctx));
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testFormatCpfRuleName(): void
    {
        $this->assertSame('brazilian.format_cpf', new FormatCpfRule()->getName());
    }

    #[Test]
    public function testFormatCnpjRuleName(): void
    {
        $this->assertSame('brazilian.format_cnpj', new FormatCnpjRule()->getName());
    }

    #[Test]
    public function testFormatCepRuleName(): void
    {
        $this->assertSame('brazilian.format_cep', new FormatCepRule()->getName());
    }
}
