<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use PHPUnit\Framework\TestCase;

final class HtmlSpecialCharsSanitizerTest extends TestCase
{
    public function testHtmlSpecialCharsSanitizer(): void
    {
        $sanitizer = new HtmlSpecialCharsSanitizer();
        $this->assertEquals(
            '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;',
            $sanitizer->process('<script>alert("xss")</script>')
        );
    }
}
