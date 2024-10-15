<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Domain;

use KaririCode\Sanitizer\Processor\Domain\JsonSanitizer;
use PHPUnit\Framework\TestCase;

final class JsonSanitizerTest extends TestCase
{
    public function testJsonSanitizer(): void
    {
        $sanitizer = new JsonSanitizer();
        $input = '{"name":"John","age":30}';
        $expected = "{\n    \"name\": \"John\",\n    \"age\": 30\n}";
        $this->assertEquals($expected, $sanitizer->process($input));
    }

    public function testJsonSanitizerWithInvalidJson(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $sanitizer = new JsonSanitizer();
        $sanitizer->process('{invalid json}');
    }
}
