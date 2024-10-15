<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Input;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use PHPUnit\Framework\TestCase;

final class TrimSanitizerTest extends TestCase
{
    public function testTrimSanitizer(): void
    {
        $sanitizer = new TrimSanitizer();
        $this->assertEquals('test', $sanitizer->process('  test  '));
        $this->assertEquals('test', $sanitizer->process("\ntest\n"));

        $sanitizer->configure(['characterMask' => 'a']);
        $this->assertEquals('test', $sanitizer->process('aaatestaa'));
    }

    public function testTrimSanitizerWithNonString(): void
    {
        $this->expectException(SanitizationException::class);
        $sanitizer = new TrimSanitizer();
        $sanitizer->process(123);
    }
}
