<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Integration;

use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversNothing]
final class FullRuleRegistrationTest extends TestCase
{
    #[Test]
    public function testAllRulesResolvable(): void
    {
        $registry = new SanitizerServiceProvider()->createRegistry();

        foreach ($registry->aliases() as $alias) {
            $rule = $registry->resolve($alias);
            $this->assertNotEmpty($rule->getName(), "Rule '{$alias}' has empty name.");
        }
    }

    #[Test]
    public function testFullPipelineIntegration(): void
    {
        $engine = new SanitizerServiceProvider()->createEngine();

        $result = $engine->sanitize(
            [
                'name' => '  walmir  SILVA  ',
                'email' => '  Admin@Kariricode.ORG  ',
                'cpf' => '52998224725',
                'bio' => 'A very long biography that should be truncated at some point for display',
                'score' => '42.678',
                'active' => 'yes',
                'date' => '28/02/2025',
                'html' => '<script>alert("xss")</script><b>Bold</b>',
                'tags' => 'hello  world  test',
            ],
            [
                'name' => ['trim', 'normalize_whitespace', 'capitalize'],
                'email' => ['trim', 'lower_case', 'email_filter'],
                'cpf' => ['format_cpf'],
                'bio' => [['truncate', ['max' => 30]]],
                'score' => ['to_float', ['round', ['precision' => 1]]],
                'active' => ['to_bool'],
                'date' => [['normalize_date', ['from' => 'd/m/Y', 'to' => 'Y-m-d']]],
                'html' => ['strip_tags'],
                'tags' => ['trim', 'normalize_whitespace', 'slug'],
            ],
        );

        $this->assertSame('Walmir Silva', $result->get('name'));
        $this->assertSame('admin@kariricode.org', $result->get('email'));
        $this->assertSame('529.982.247-25', $result->get('cpf'));
        $this->assertSame('A very long biography that ...', $result->get('bio'));
        $this->assertSame(42.7, $result->get('score'));
        $this->assertTrue($result->get('active'));
        $this->assertSame('2025-02-28', $result->get('date'));
        $this->assertSame('alert("xss")Bold', $result->get('html'));
        $this->assertSame('hello-world-test', $result->get('tags'));
    }
}
