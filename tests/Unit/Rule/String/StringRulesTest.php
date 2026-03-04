<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\String;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\String\{
    TrimRule, LowerCaseRule, UpperCaseRule, CapitalizeRule, SlugRule,
    TruncateRule, NormalizeWhitespaceRule, NormalizeLineEndingsRule,
    PadRule, ReplaceRule, RegexReplaceRule, StripNonPrintableRule
};
use PHPUnit\Framework\TestCase;

final class StringRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    // ── Trim ──────────────────────────────────────────────────────

    public function testTrimDefault(): void
    {
        $this->assertSame('hello', (new TrimRule())->sanitize('  hello  ', $this->ctx()));
    }

    public function testTrimCustomCharacters(): void
    {
        $this->assertSame('hello', (new TrimRule())->sanitize('xxhelloxx', $this->ctx(['characters' => 'x'])));
    }

    public function testTrimNonString(): void
    {
        $this->assertSame(42, (new TrimRule())->sanitize(42, $this->ctx()));
    }

    // ── LowerCase ─────────────────────────────────────────────────

    public function testLowerCase(): void
    {
        $this->assertSame('hello world', (new LowerCaseRule())->sanitize('Hello WORLD', $this->ctx()));
    }

    public function testLowerCaseUnicode(): void
    {
        $this->assertSame('são paulo', (new LowerCaseRule())->sanitize('SÃO PAULO', $this->ctx()));
    }

    // ── UpperCase ─────────────────────────────────────────────────

    public function testUpperCase(): void
    {
        $this->assertSame('HELLO WORLD', (new UpperCaseRule())->sanitize('Hello World', $this->ctx()));
    }

    // ── Capitalize ────────────────────────────────────────────────

    public function testCapitalize(): void
    {
        $this->assertSame('Hello World', (new CapitalizeRule())->sanitize('hello world', $this->ctx()));
    }

    public function testCapitalizeUnicode(): void
    {
        $this->assertSame('São Paulo', (new CapitalizeRule())->sanitize('são paulo', $this->ctx()));
    }

    // ── Slug ──────────────────────────────────────────────────────

    public function testSlug(): void
    {
        $this->assertSame('hello-world', (new SlugRule())->sanitize('Hello World!', $this->ctx()));
    }

    public function testSlugAccented(): void
    {
        $this->assertSame('sao-paulo-brasil', (new SlugRule())->sanitize('São Paulo, Brasil!', $this->ctx()));
    }

    public function testSlugCustomSeparator(): void
    {
        $this->assertSame('hello_world', (new SlugRule())->sanitize('Hello World', $this->ctx(['separator' => '_'])));
    }

    // ── Truncate ──────────────────────────────────────────────────

    public function testTruncate(): void
    {
        $this->assertSame('Hello...', (new TruncateRule())->sanitize('Hello World', $this->ctx(['max' => 8])));
    }

    public function testTruncateNoTruncationNeeded(): void
    {
        $this->assertSame('Hi', (new TruncateRule())->sanitize('Hi', $this->ctx(['max' => 10])));
    }

    public function testTruncateCustomSuffix(): void
    {
        $this->assertSame('Hell…', (new TruncateRule())->sanitize('Hello World', $this->ctx(['max' => 5, 'suffix' => '…'])));
    }

    // ── NormalizeWhitespace ───────────────────────────────────────

    public function testNormalizeWhitespace(): void
    {
        $this->assertSame('hello world', (new NormalizeWhitespaceRule())->sanitize("  hello   \t  world  ", $this->ctx()));
    }

    // ── NormalizeLineEndings ──────────────────────────────────────

    public function testNormalizeLineEndings(): void
    {
        $this->assertSame("a\nb\nc", (new NormalizeLineEndingsRule())->sanitize("a\r\nb\rc", $this->ctx()));
    }

    // ── Pad ───────────────────────────────────────────────────────

    public function testPadRight(): void
    {
        $this->assertSame('hi   ', (new PadRule())->sanitize('hi', $this->ctx(['length' => 5])));
    }

    public function testPadLeft(): void
    {
        $this->assertSame('007', (new PadRule())->sanitize('7', $this->ctx(['length' => 3, 'pad' => '0', 'side' => 'left'])));
    }

    // ── Replace ───────────────────────────────────────────────────

    public function testReplace(): void
    {
        $this->assertSame('Hi World', (new ReplaceRule())->sanitize('Hello World', $this->ctx(['search' => 'Hello', 'replace' => 'Hi'])));
    }

    public function testReplaceEmptySearch(): void
    {
        $this->assertSame('Hello', (new ReplaceRule())->sanitize('Hello', $this->ctx()));
    }

    // ── RegexReplace ──────────────────────────────────────────────

    public function testRegexReplace(): void
    {
        $result = (new RegexReplaceRule())->sanitize('abc123def', $this->ctx(['pattern' => '/\d+/', 'replacement' => '#']));
        $this->assertSame('abc#def', $result);
    }

    public function testRegexReplaceEmptyPattern(): void
    {
        $this->assertSame('test', (new RegexReplaceRule())->sanitize('test', $this->ctx()));
    }

    // ── StripNonPrintable ─────────────────────────────────────────

    public function testStripNonPrintable(): void
    {
        $this->assertSame("hello\nworld", (new StripNonPrintableRule())->sanitize("hel\x00lo\nwor\x07ld", $this->ctx()));
    }

    public function testStripNonPrintablePreservesTabsAndCR(): void
    {
        $this->assertSame("a\tb\r\nc", (new StripNonPrintableRule())->sanitize("a\tb\r\nc", $this->ctx()));
    }
}
