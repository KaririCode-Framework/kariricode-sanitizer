<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\String;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\String\CapitalizeRule;
use KaririCode\Sanitizer\Rule\String\LowerCaseRule;
use KaririCode\Sanitizer\Rule\String\NormalizeLineEndingsRule;
use KaririCode\Sanitizer\Rule\String\NormalizeWhitespaceRule;
use KaririCode\Sanitizer\Rule\String\PadRule;
use KaririCode\Sanitizer\Rule\String\RegexReplaceRule;
use KaririCode\Sanitizer\Rule\String\ReplaceRule;
use KaririCode\Sanitizer\Rule\String\SlugRule;
use KaririCode\Sanitizer\Rule\String\StripNonPrintableRule;
use KaririCode\Sanitizer\Rule\String\TrimRule;
use KaririCode\Sanitizer\Rule\String\TruncateRule;
use KaririCode\Sanitizer\Rule\String\UpperCaseRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(TrimRule::class)]
#[CoversClass(LowerCaseRule::class)]
#[CoversClass(UpperCaseRule::class)]
#[CoversClass(CapitalizeRule::class)]
#[CoversClass(SlugRule::class)]
#[CoversClass(TruncateRule::class)]
#[CoversClass(NormalizeWhitespaceRule::class)]
#[CoversClass(NormalizeLineEndingsRule::class)]
#[CoversClass(PadRule::class)]
#[CoversClass(ReplaceRule::class)]
#[CoversClass(RegexReplaceRule::class)]
#[CoversClass(StripNonPrintableRule::class)]
final class StringRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    // ── Trim ──────────────────────────────────────────────────────

    #[Test]
    public function testTrimDefault(): void
    {
        $this->assertSame('hello', new TrimRule()->sanitize('  hello  ', $this->ctx()));
    }

    #[Test]
    public function testTrimCustomCharacters(): void
    {
        $this->assertSame('hello', new TrimRule()->sanitize('xxhelloxx', $this->ctx(['characters' => 'x'])));
    }

    #[Test]
    public function testTrimNonString(): void
    {
        $this->assertSame(42, new TrimRule()->sanitize(42, $this->ctx()));
    }

    // ── LowerCase ─────────────────────────────────────────────────

    #[Test]
    public function testLowerCase(): void
    {
        $this->assertSame('hello world', new LowerCaseRule()->sanitize('Hello WORLD', $this->ctx()));
    }

    #[Test]
    public function testLowerCaseUnicode(): void
    {
        $this->assertSame('são paulo', new LowerCaseRule()->sanitize('SÃO PAULO', $this->ctx()));
    }

    // ── UpperCase ─────────────────────────────────────────────────

    #[Test]
    public function testUpperCase(): void
    {
        $this->assertSame('HELLO WORLD', new UpperCaseRule()->sanitize('Hello World', $this->ctx()));
    }

    // ── Capitalize ────────────────────────────────────────────────

    #[Test]
    public function testCapitalize(): void
    {
        $this->assertSame('Hello World', new CapitalizeRule()->sanitize('hello world', $this->ctx()));
    }

    #[Test]
    public function testCapitalizeUnicode(): void
    {
        $this->assertSame('São Paulo', new CapitalizeRule()->sanitize('são paulo', $this->ctx()));
    }

    // ── Slug ──────────────────────────────────────────────────────

    #[Test]
    public function testSlug(): void
    {
        $this->assertSame('hello-world', new SlugRule()->sanitize('Hello World!', $this->ctx()));
    }

    #[Test]
    public function testSlugAccented(): void
    {
        $this->assertSame('sao-paulo-brasil', new SlugRule()->sanitize('São Paulo, Brasil!', $this->ctx()));
    }

    #[Test]
    public function testSlugCustomSeparator(): void
    {
        $this->assertSame('hello_world', new SlugRule()->sanitize('Hello World', $this->ctx(['separator' => '_'])));
    }

    // ── Truncate ──────────────────────────────────────────────────

    #[Test]
    public function testTruncate(): void
    {
        $this->assertSame('Hello...', new TruncateRule()->sanitize('Hello World', $this->ctx(['max' => 8])));
    }

    #[Test]
    public function testTruncateNoTruncationNeeded(): void
    {
        $this->assertSame('Hi', new TruncateRule()->sanitize('Hi', $this->ctx(['max' => 10])));
    }

    #[Test]
    public function testTruncateCustomSuffix(): void
    {
        $this->assertSame('Hell…', new TruncateRule()->sanitize('Hello World', $this->ctx(['max' => 5, 'suffix' => '…'])));
    }

    // ── NormalizeWhitespace ───────────────────────────────────────

    #[Test]
    public function testNormalizeWhitespace(): void
    {
        $this->assertSame('hello world', new NormalizeWhitespaceRule()->sanitize("  hello   \t  world  ", $this->ctx()));
    }

    // ── NormalizeLineEndings ──────────────────────────────────────

    #[Test]
    public function testNormalizeLineEndings(): void
    {
        $this->assertSame("a\nb\nc", new NormalizeLineEndingsRule()->sanitize("a\r\nb\rc", $this->ctx()));
    }

    // ── Pad ───────────────────────────────────────────────────────

    #[Test]
    public function testPadRight(): void
    {
        $this->assertSame('hi   ', new PadRule()->sanitize('hi', $this->ctx(['length' => 5])));
    }

    #[Test]
    public function testPadLeft(): void
    {
        $this->assertSame('007', new PadRule()->sanitize('7', $this->ctx(['length' => 3, 'pad' => '0', 'side' => 'left'])));
    }

    // ── Replace ───────────────────────────────────────────────────

    #[Test]
    public function testReplace(): void
    {
        $this->assertSame('Hi World', new ReplaceRule()->sanitize('Hello World', $this->ctx(['search' => 'Hello', 'replace' => 'Hi'])));
    }

    #[Test]
    public function testReplaceEmptySearch(): void
    {
        $this->assertSame('Hello', new ReplaceRule()->sanitize('Hello', $this->ctx()));
    }

    // ── RegexReplace ──────────────────────────────────────────────

    #[Test]
    public function testRegexReplace(): void
    {
        $result = new RegexReplaceRule()->sanitize('abc123def', $this->ctx(['pattern' => '/\d+/', 'replacement' => '#']));
        $this->assertSame('abc#def', $result);
    }

    #[Test]
    public function testRegexReplaceEmptyPattern(): void
    {
        $this->assertSame('test', new RegexReplaceRule()->sanitize('test', $this->ctx()));
    }

    // ── StripNonPrintable ─────────────────────────────────────────

    #[Test]
    public function testStripNonPrintable(): void
    {
        $this->assertSame("hello\nworld", new StripNonPrintableRule()->sanitize("hel\x00lo\nwor\x07ld", $this->ctx()));
    }

    #[Test]
    public function testStripNonPrintablePreservesTabsAndCR(): void
    {
        $this->assertSame("a\tb\r\nc", new StripNonPrintableRule()->sanitize("a\tb\r\nc", $this->ctx()));
    }

    #[Test]
    public function testStripNonPrintableNonString(): void
    {
        $this->assertSame(99, new StripNonPrintableRule()->sanitize(99, $this->ctx()));
    }

    // ── Non-string passthrough (each rule must guard !is_string) ──

    #[Test]
    public function testLowerCaseNonString(): void
    {
        $this->assertSame(42, new LowerCaseRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testUpperCaseNonString(): void
    {
        $this->assertSame(42, new UpperCaseRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testCapitalizeNonString(): void
    {
        $this->assertSame(42, new CapitalizeRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testSlugNonString(): void
    {
        $this->assertSame(42, new SlugRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testNormalizeWhitespaceNonString(): void
    {
        $this->assertSame(42, new NormalizeWhitespaceRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testNormalizeLineEndingsNonString(): void
    {
        $this->assertSame(42, new NormalizeLineEndingsRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testRegexReplaceNonString(): void
    {
        $this->assertSame(42, new RegexReplaceRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testReplaceNonString(): void
    {
        $this->assertSame(42, new ReplaceRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testTruncateNonString(): void
    {
        $this->assertSame(42, new TruncateRule()->sanitize(42, $this->ctx()));
    }

    // ── PadRule 'both' side ───────────────────────────────────────

    #[Test]
    public function testPadBoth(): void
    {
        $this->assertSame('-hi--', new PadRule()->sanitize('hi', $this->ctx(['length' => 5, 'pad' => '-', 'side' => 'both'])));
    }

    #[Test]
    public function testPadNonString(): void
    {
        $this->assertSame(42, new PadRule()->sanitize(42, $this->ctx()));
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testTrimRuleName(): void
    {
        $this->assertSame('string.trim', new TrimRule()->getName());
    }

    #[Test]
    public function testLowerCaseRuleName(): void
    {
        $this->assertSame('string.lower_case', new LowerCaseRule()->getName());
    }

    #[Test]
    public function testUpperCaseRuleName(): void
    {
        $this->assertSame('string.upper_case', new UpperCaseRule()->getName());
    }

    #[Test]
    public function testCapitalizeRuleName(): void
    {
        $this->assertSame('string.capitalize', new CapitalizeRule()->getName());
    }

    #[Test]
    public function testSlugRuleName(): void
    {
        $this->assertSame('string.slug', new SlugRule()->getName());
    }

    #[Test]
    public function testTruncateRuleName(): void
    {
        $this->assertSame('string.truncate', new TruncateRule()->getName());
    }

    #[Test]
    public function testNormalizeWhitespaceRuleName(): void
    {
        $this->assertSame('string.normalize_whitespace', new NormalizeWhitespaceRule()->getName());
    }

    #[Test]
    public function testNormalizeLineEndingsRuleName(): void
    {
        $this->assertSame('string.normalize_line_endings', new NormalizeLineEndingsRule()->getName());
    }

    #[Test]
    public function testPadRuleName(): void
    {
        $this->assertSame('string.pad', new PadRule()->getName());
    }

    #[Test]
    public function testReplaceRuleName(): void
    {
        $this->assertSame('string.replace', new ReplaceRule()->getName());
    }

    #[Test]
    public function testRegexReplaceRuleName(): void
    {
        $this->assertSame('string.regex_replace', new RegexReplaceRule()->getName());
    }

    #[Test]
    public function testStripNonPrintableRuleName(): void
    {
        $this->assertSame('string.strip_non_printable', new StripNonPrintableRule()->getName());
    }
}
