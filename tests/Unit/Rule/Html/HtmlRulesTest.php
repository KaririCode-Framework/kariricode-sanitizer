<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Html;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Html\HtmlDecodeRule;
use KaririCode\Sanitizer\Rule\Html\HtmlEncodeRule;
use KaririCode\Sanitizer\Rule\Html\HtmlPurifyRule;
use KaririCode\Sanitizer\Rule\Html\StripTagsRule;
use KaririCode\Sanitizer\Rule\Html\UrlEncodeRule;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(StripTagsRule::class)]
#[CoversClass(HtmlEncodeRule::class)]
#[CoversClass(HtmlDecodeRule::class)]
#[CoversClass(HtmlPurifyRule::class)]
#[CoversClass(UrlEncodeRule::class)]
final class HtmlRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    #[Test]
    public function testStripTags(): void
    {
        $this->assertSame('hello', new StripTagsRule()->sanitize('<b>hello</b>', $this->ctx()));
    }

    #[Test]
    public function testStripTagsWithAllowed(): void
    {
        $this->assertSame('<b>hello</b>x', new StripTagsRule()->sanitize('<b>hello</b><script>x</script>', $this->ctx(['allowed' => '<b>'])));
    }

    #[Test]
    public function testHtmlEncode(): void
    {
        $this->assertSame('&lt;script&gt;', new HtmlEncodeRule()->sanitize('<script>', $this->ctx()));
    }

    #[Test]
    public function testHtmlEncodeQuotes(): void
    {
        $result = new HtmlEncodeRule()->sanitize('He said "hello" & \'bye\'', $this->ctx());
        $this->assertSame('He said &quot;hello&quot; &amp; &#039;bye&#039;', $result);
    }

    #[Test]
    public function testHtmlDecode(): void
    {
        $this->assertSame('<script>', new HtmlDecodeRule()->sanitize('&lt;script&gt;', $this->ctx()));
    }

    #[Test]
    public function testHtmlPurify(): void
    {
        $this->assertSame('hello alert', new HtmlPurifyRule()->sanitize(' <b>hello</b> <script>alert</script> ', $this->ctx()));
    }

    #[Test]
    public function testUrlEncode(): void
    {
        $this->assertSame('hello+world', new UrlEncodeRule()->sanitize('hello world', $this->ctx()));
    }

    #[Test]
    public function testUrlEncodeRaw(): void
    {
        $this->assertSame('hello%20world', new UrlEncodeRule()->sanitize('hello world', $this->ctx(['raw' => true])));
    }

    // ── Non-string passthrough ────────────────────────────────────

    #[Test]
    public function testStripTagsNonString(): void
    {
        $this->assertSame(42, new StripTagsRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testHtmlEncodeNonString(): void
    {
        $this->assertSame(42, new HtmlEncodeRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testHtmlEncodeDoubleEncodeFalse(): void
    {
        $result = new HtmlEncodeRule()->sanitize('&amp;', $this->ctx(['double_encode' => false]));
        $this->assertSame('&amp;', $result);
    }

    #[Test]
    public function testHtmlDecodeNonString(): void
    {
        $this->assertSame(42, new HtmlDecodeRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testHtmlPurifyNonString(): void
    {
        $this->assertSame(42, new HtmlPurifyRule()->sanitize(42, $this->ctx()));
    }

    #[Test]
    public function testUrlEncodeNonString(): void
    {
        $this->assertSame(42, new UrlEncodeRule()->sanitize(42, $this->ctx()));
    }

    // ── Rule names (constant values — one assertSame per method) ──

    #[Test]
    public function testStripTagsRuleName(): void
    {
        $this->assertSame('html.strip_tags', new StripTagsRule()->getName());
    }

    #[Test]
    public function testHtmlEncodeRuleName(): void
    {
        $this->assertSame('html.encode', new HtmlEncodeRule()->getName());
    }

    #[Test]
    public function testHtmlDecodeRuleName(): void
    {
        $this->assertSame('html.decode', new HtmlDecodeRule()->getName());
    }

    #[Test]
    public function testHtmlPurifyRuleName(): void
    {
        $this->assertSame('html.purify', new HtmlPurifyRule()->getName());
    }

    #[Test]
    public function testUrlEncodeRuleName(): void
    {
        $this->assertSame('html.url_encode', new UrlEncodeRule()->getName());
    }
}
