<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Attribute;

use KaririCode\Contract\Processor\Attribute\CustomizableMessageAttribute;
use KaririCode\Contract\Processor\Attribute\ProcessableAttribute;
use KaririCode\Sanitizer\Attribute\Sanitize;
use PHPUnit\Framework\TestCase;

final class SanitizeTest extends TestCase
{
    public function testSanitizeImplementsProcessableAttribute(): void
    {
        $sanitize = new Sanitize([]);
        $this->assertInstanceOf(ProcessableAttribute::class, $sanitize);
    }

    public function testSanitizeImplementsCustomizableMessageAttribute(): void
    {
        $sanitize = new Sanitize([]);
        $this->assertInstanceOf(CustomizableMessageAttribute::class, $sanitize);
    }

    public function testSanitizeIsAttribute(): void
    {
        $reflectionClass = new \ReflectionClass(Sanitize::class);
        $attributes = $reflectionClass->getAttributes();

        $this->assertCount(1, $attributes);
        $this->assertSame(\Attribute::class, $attributes[0]->getName());
        $this->assertSame([\Attribute::TARGET_PROPERTY], $attributes[0]->getArguments());
    }

    public function testConstructorFiltersInvalidProcessors(): void
    {
        $processors = ['trim', null, false, 'htmlspecialchars'];
        $expectedProcessors = ['trim', 'htmlspecialchars'];
        $sanitize = new Sanitize($processors);

        $this->assertSame(array_values($expectedProcessors), array_values($sanitize->getProcessors()));
    }

    public function testGetProcessorsReturnsProcessors(): void
    {
        $processors = ['trim', 'htmlspecialchars'];
        $sanitize = new Sanitize($processors);

        $this->assertSame($processors, $sanitize->getProcessors());
    }

    public function testGetMessageReturnsNullWhenNoMessagesProvided(): void
    {
        $sanitize = new Sanitize(['trim']);
        $this->assertNull($sanitize->getMessage('trim'));
    }

    public function testGetMessageReturnsCustomMessage(): void
    {
        $messages = ['trim' => 'Trim applied'];
        $sanitize = new Sanitize(['trim'], $messages);

        $this->assertSame('Trim applied', $sanitize->getMessage('trim'));
    }

    public function testGetMessageReturnsNullForUnknownProcessor(): void
    {
        $messages = ['trim' => 'Trim applied'];
        $sanitize = new Sanitize(['trim'], $messages);

        $this->assertNull($sanitize->getMessage('htmlspecialchars'));
    }
}
