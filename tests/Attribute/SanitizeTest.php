<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Attribute;

use KaririCode\Contract\Processor\ProcessableAttribute;
use KaririCode\Sanitizer\Attribute\Sanitize;
use PHPUnit\Framework\TestCase;

final class SanitizeTest extends TestCase
{
    public function testSanitizeImplementsProcessableAttribute(): void
    {
        $this->assertInstanceOf(ProcessableAttribute::class, new Sanitize([]));
    }

    public function testSanitizeIsAttribute(): void
    {
        $reflectionClass = new \ReflectionClass(Sanitize::class);
        $attributes = $reflectionClass->getAttributes();

        $this->assertCount(1, $attributes);
        $this->assertSame(\Attribute::class, $attributes[0]->getName());
        $this->assertSame([\Attribute::TARGET_PROPERTY], $attributes[0]->getArguments());
    }

    public function testConstructorSetsSanitizers(): void
    {
        $sanitizers = ['trim', 'htmlspecialchars'];
        $sanitize = new Sanitize($sanitizers);

        $this->assertSame($sanitizers, $sanitize->sanitizers);
    }

    public function testConstructorSetsFallbackValue(): void
    {
        $fallbackValue = 'default';
        $sanitize = new Sanitize([], $fallbackValue);

        $this->assertSame($fallbackValue, $sanitize->fallbackValue);
    }

    public function testConstructorSetsNullFallbackValueByDefault(): void
    {
        $sanitize = new Sanitize([]);

        $this->assertNull($sanitize->fallbackValue);
    }

    public function testGetProcessorsReturnsSanitizers(): void
    {
        $sanitizers = ['trim', 'htmlspecialchars'];
        $sanitize = new Sanitize($sanitizers);

        $this->assertSame($sanitizers, $sanitize->getProcessors());
    }

    public function testGetFallbackValueReturnsFallbackValue(): void
    {
        $fallbackValue = 'default';
        $sanitize = new Sanitize([], $fallbackValue);

        $this->assertSame($fallbackValue, $sanitize->getFallbackValue());
    }

    public function testSanitizeWithMultipleArguments(): void
    {
        $sanitizers = ['trim', 'htmlspecialchars'];
        $fallbackValue = 'default';
        $sanitize = new Sanitize($sanitizers, $fallbackValue);

        $this->assertSame($sanitizers, $sanitize->sanitizers);
        $this->assertSame($fallbackValue, $sanitize->fallbackValue);
        $this->assertSame($sanitizers, $sanitize->getProcessors());
        $this->assertSame($fallbackValue, $sanitize->getFallbackValue());
    }
}
