<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Core;

use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;
use PHPUnit\Framework\TestCase;

final class SanitizerEngineTest extends TestCase
{
    public function testBasicSanitization(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['name' => '  Hello World  ', 'email' => '  User@Test.COM  '],
            [
                'name' => ['trim'],
                'email' => ['trim', 'lower_case'],
            ],
        );

        $this->assertSame('Hello World', $result->get('name'));
        $this->assertSame('user@test.com', $result->get('email'));
        $this->assertTrue($result->wasModified());
    }

    public function testParameterizedRules(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['bio' => 'This is a very long biography text for testing'],
            ['bio' => [['truncate', ['max' => 20, 'suffix' => '…']]]],
        );

        $this->assertSame('This is a very long…', $result->get('bio'));
    }

    public function testModificationTracking(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['name' => '  Walmir  ', 'untouched' => 'hello'],
            [
                'name' => ['trim'],
                'untouched' => ['trim'],
            ],
        );

        $this->assertTrue($result->isFieldModified('name'));
        $this->assertFalse($result->isFieldModified('untouched'));
        $this->assertSame(['name'], $result->modifiedFields());
        $this->assertSame(1, $result->modificationCount());
    }

    public function testDotNotationResolution(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['user' => ['name' => '  Walmir  ']],
            ['user.name' => ['trim']],
        );

        $this->assertSame('Walmir', $result->get('user.name'));
    }

    public function testPipelineOrdering(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['tag' => '  Hello World  '],
            ['tag' => ['trim', 'lower_case', 'slug']],
        );

        $this->assertSame('hello-world', $result->get('tag'));
    }

    public function testOriginalDataPreserved(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['name' => '  test  '],
            ['name' => ['trim']],
        );

        $this->assertSame('  test  ', $result->getOriginalData()['name']);
        $this->assertSame('test', $result->getSanitizedData()['name']);
    }

    public function testModificationsLog(): void
    {
        $engine = (new SanitizerServiceProvider())->createEngine();

        $result = $engine->sanitize(
            ['name' => '  Walmir  '],
            ['name' => ['trim', 'upper_case']],
        );

        $mods = $result->modificationsFor('name');
        $this->assertCount(2, $mods);
        $this->assertSame('string.trim', $mods[0]->ruleName);
        $this->assertSame('  Walmir  ', $mods[0]->before);
        $this->assertSame('Walmir', $mods[0]->after);
        $this->assertSame('string.upper_case', $mods[1]->ruleName);
        $this->assertSame('WALMIR', $mods[1]->after);
    }
}
