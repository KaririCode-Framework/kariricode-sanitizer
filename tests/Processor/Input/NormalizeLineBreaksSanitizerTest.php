<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;
use PHPUnit\Framework\TestCase;

final class NormalizeLineBreaksSanitizerTest extends TestCase
{
    public function testNormalizeLineBreaksSanitizer(): void
    {
        $sanitizer = new NormalizeLineBreaksSanitizer();
        $this->assertEquals(
            "line1\nline2\nline3",
            $sanitizer->process("line1\r\nline2\rline3")
        );
    }
}
