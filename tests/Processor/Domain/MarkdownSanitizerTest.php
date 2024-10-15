<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Domain;

use KaririCode\Sanitizer\Processor\Domain\MarkdownSanitizer;
use PHPUnit\Framework\TestCase;

final class MarkdownSanitizerTest extends TestCase
{
    public function testMarkdownSanitizer(): void
    {
        $sanitizer = new MarkdownSanitizer();
        $this->assertEquals(
            'This is \*emphasized\* and this is \*\*bold\*\*',
            $sanitizer->process('This is *emphasized* and this is **bold**')
        );
        $this->assertEquals('\\# Heading', $sanitizer->process('# Heading'));
    }
}
