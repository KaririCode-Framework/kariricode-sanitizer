<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Rule\Html;

use KaririCode\Sanitizer\Core\SanitizationContextImpl;
use KaririCode\Sanitizer\Rule\Html\{StripTagsRule, HtmlEncodeRule, HtmlDecodeRule, HtmlPurifyRule, UrlEncodeRule};
use PHPUnit\Framework\TestCase;

final class HtmlRulesTest extends TestCase
{
    private function ctx(array $params = []): \KaririCode\Sanitizer\Contract\SanitizationContext
    {
        return SanitizationContextImpl::create([])->withField('test')->withParameters($params);
    }

    public function testStripTags(): void
    {
        $this->assertSame('hello', (new StripTagsRule())->sanitize('<b>hello</b>', $this->ctx()));
    }

    public function testStripTagsWithAllowed(): void
    {
        $this->assertSame('<b>hello</b>x', (new StripTagsRule())->sanitize('<b>hello</b><script>x</script>', $this->ctx(['allowed' => '<b>'])));
    }

    public function testHtmlEncode(): void
    {
        $this->assertSame('&lt;script&gt;', (new HtmlEncodeRule())->sanitize('<script>', $this->ctx()));
    }

    public function testHtmlEncodeQuotes(): void
    {
        $result = (new HtmlEncodeRule())->sanitize('He said "hello" & \'bye\'', $this->ctx());
        $this->assertSame('He said &quot;hello&quot; &amp; &#039;bye&#039;', $result);
    }

    public function testHtmlDecode(): void
    {
        $this->assertSame('<script>', (new HtmlDecodeRule())->sanitize('&lt;script&gt;', $this->ctx()));
    }

    public function testHtmlPurify(): void
    {
        $this->assertSame('hello alert', (new HtmlPurifyRule())->sanitize(' <b>hello</b> <script>alert</script> ', $this->ctx()));
    }

    public function testUrlEncode(): void
    {
        $this->assertSame('hello+world', (new UrlEncodeRule())->sanitize('hello world', $this->ctx()));
    }

    public function testUrlEncodeRaw(): void
    {
        $this->assertSame('hello%20world', (new UrlEncodeRule())->sanitize('hello world', $this->ctx(['raw' => true])));
    }
}
