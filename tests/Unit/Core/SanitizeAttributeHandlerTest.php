<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Core;

use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Core\SanitizeAttributeHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SanitizeAttributeHandler::class)]
final class SanitizeAttributeHandlerTest extends TestCase
{
    #[Test]
    public function testHandleAttributeCollectsRulesAndData(): void
    {
        $handler = new SanitizeAttributeHandler();
        $attr = new Sanitize('trim', 'lower_case');

        $handler->handleAttribute('email', $attr, '  TEST@EXAMPLE.COM  ');

        $this->assertSame(['email' => '  TEST@EXAMPLE.COM  '], $handler->getProcessedPropertyValues());
        $this->assertSame(['email' => ['trim', 'lower_case']], $handler->getFieldRules());
    }

    #[Test]
    public function testHandleAttributeMergesMultipleAttributesOnSameProperty(): void
    {
        $handler = new SanitizeAttributeHandler();
        $attr1 = new Sanitize('trim');
        $attr2 = new Sanitize('capitalize');

        $handler->handleAttribute('name', $attr1, '  hello  ');
        $handler->handleAttribute('name', $attr2, '  hello  ');

        $this->assertSame(['name' => ['trim', 'capitalize']], $handler->getFieldRules());
    }

    #[Test]
    public function testHandleAttributeWithNonSanitizeAttributeReturnsNull(): void
    {
        $handler = new SanitizeAttributeHandler();
        $nonSanitize = new \stdClass();

        $result = $handler->handleAttribute('field', $nonSanitize, 'value');

        $this->assertNull($result);
        $this->assertSame([], $handler->getFieldRules());
    }

    #[Test]
    public function testGetProcessingResultMessagesReturnsEmpty(): void
    {
        $handler = new SanitizeAttributeHandler();
        $this->assertSame([], $handler->getProcessingResultMessages());
    }

    #[Test]
    public function testGetProcessingResultErrorsReturnsEmpty(): void
    {
        $handler = new SanitizeAttributeHandler();
        $this->assertSame([], $handler->getProcessingResultErrors());
    }

    #[Test]
    public function testSetProcessedValuesAndApplyChanges(): void
    {
        $handler = new SanitizeAttributeHandler();

        $obj = new class () {
            public string $name = 'original';
        };

        $handler->setProcessedValues(['name' => 'updated']);
        $handler->applyChanges($obj);

        $this->assertSame('updated', $obj->name);
    }

    #[Test]
    public function testApplyChangesSkipsMissingProperty(): void
    {
        $handler = new SanitizeAttributeHandler();

        $obj = new class () {
            public string $name = 'original';
        };

        // 'nonexistent' does not exist on the object — must not throw
        $handler->setProcessedValues(['nonexistent' => 'value']);
        $handler->applyChanges($obj);

        $this->assertSame('original', $obj->name);
    }
}
