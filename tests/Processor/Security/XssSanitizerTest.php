<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Security;

use KaririCode\Sanitizer\Processor\Security\XssSanitizer;
use PHPUnit\Framework\TestCase;

final class XssSanitizerTest extends TestCase
{
    public function testXssSanitizer(): void
    {
        $sanitizer = new XssSanitizer();
        $this->assertEquals(
            '&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;',
            $sanitizer->process('<script>alert("xss")</script>')
        );
    }
}
