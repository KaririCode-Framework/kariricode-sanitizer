<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Provider;

use KaririCode\Sanitizer\Core\AttributeSanitizer;
use KaririCode\Sanitizer\Core\SanitizerEngine;
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;
use PHPUnit\Framework\TestCase;

final class SanitizerServiceProviderTest extends TestCase
{
    private const EXPECTED_ALIASES = [
        // String (12)
        'trim', 'lower_case', 'upper_case', 'capitalize', 'slug',
        'truncate', 'normalize_whitespace', 'normalize_line_endings',
        'pad', 'replace', 'regex_replace', 'strip_non_printable',
        // Html (5)
        'strip_tags', 'html_encode', 'html_decode', 'html_purify', 'url_encode',
        // Numeric (4)
        'to_int', 'to_float', 'clamp', 'round',
        // Type (3)
        'to_bool', 'to_string', 'to_array',
        // Date (2)
        'normalize_date', 'timestamp_to_date',
        // Filter (4)
        'digits_only', 'alpha_only', 'alphanumeric_only', 'email_filter',
        // Brazilian (3)
        'format_cpf', 'format_cnpj', 'format_cep',
    ];

    public function testRegistersAll33Aliases(): void
    {
        $registry = (new SanitizerServiceProvider())->createRegistry();

        $this->assertCount(33, $registry->aliases());

        foreach (self::EXPECTED_ALIASES as $alias) {
            $this->assertTrue($registry->has($alias), "Missing alias: {$alias}");
        }
    }

    public function testCreateEngine(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();
        $this->assertInstanceOf(SanitizerEngine::class, $engine);
    }

    public function testCreateAttributeSanitizer(): void
    {
        $sanitizer = (new SanitizerServiceProvider())->createAttributeSanitizer();
        $this->assertInstanceOf(AttributeSanitizer::class, $sanitizer);
    }
}
