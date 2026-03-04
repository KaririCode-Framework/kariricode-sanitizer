<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Core;

use KaririCode\Sanitizer\Result\FieldModification;
use KaririCode\Sanitizer\Result\SanitizationResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SanitizationResult::class)]
#[CoversClass(FieldModification::class)]
final class SanitizationResultTest extends TestCase
{
    #[Test]
    public function testBasicGetters(): void
    {
        $result = new SanitizationResult(['name' => 'WALMIR'], ['name' => 'walmir']);
        $this->assertSame(['name' => 'WALMIR'], $result->getOriginalData());
        $this->assertSame(['name' => 'walmir'], $result->getSanitizedData());
        $this->assertSame('walmir', $result->get('name'));
        $this->assertNull($result->get('missing'));
    }

    #[Test]
    public function testWasModified(): void
    {
        $changed = new SanitizationResult(['x' => 1], ['x' => 2]);
        $unchanged = new SanitizationResult(['x' => 1], ['x' => 1]);
        $this->assertTrue($changed->wasModified());
        $this->assertFalse($unchanged->wasModified());
    }

    #[Test]
    public function testIsFieldModified(): void
    {
        $result = new SanitizationResult(['x' => 1], ['x' => 2, 'y' => 3]);
        $this->assertTrue($result->isFieldModified('x'));
        $this->assertTrue($result->isFieldModified('y')); // new key not in original
    }

    #[Test]
    public function testIsFieldModifiedFalse(): void
    {
        $result = new SanitizationResult(['x' => 1], ['x' => 1]);
        $this->assertFalse($result->isFieldModified('x'));
    }

    #[Test]
    public function testModifiedFields(): void
    {
        $result = new SanitizationResult(['a' => 1, 'b' => 2], ['a' => 99, 'b' => 2]);
        $this->assertSame(['a'], $result->modifiedFields());
    }

    #[Test]
    public function testSetSanitizedValue(): void
    {
        $result = new SanitizationResult(['x' => 1], ['x' => 1]);
        $result->setSanitizedValue('x', 42);
        $this->assertSame(42, $result->get('x'));
    }

    #[Test]
    public function testAddModificationAndGetters(): void
    {
        $result = new SanitizationResult(['x' => 'A'], ['x' => 'a']);
        $mod = new FieldModification('x', 'lower_case', 'A', 'a');
        $result->addModification($mod);

        $this->assertCount(1, $result->getModifications());
        $this->assertSame([$mod], $result->modificationsFor('x'));
        $this->assertSame([], $result->modificationsFor('missing'));
        $this->assertSame(1, $result->modificationCount());
    }

    #[Test]
    public function testModificationCountZeroWhenNotModified(): void
    {
        $result = new SanitizationResult(['x' => 'same'], ['x' => 'same']);
        $result->addModification(new FieldModification('x', 'rule', 'same', 'same'));
        $this->assertSame(0, $result->modificationCount());
    }

    #[Test]
    public function testMerge(): void
    {
        $r1 = new SanitizationResult(['a' => 1], ['a' => 2]);
        $r1->addModification(new FieldModification('a', 'rule', 1, 2));

        $r2 = new SanitizationResult(['b' => 3], ['b' => 4]);
        $r2->addModification(new FieldModification('b', 'rule', 3, 4));

        $merged = $r1->merge($r2);
        $this->assertSame(['a' => 1, 'b' => 3], $merged->getOriginalData());
        $this->assertSame(['a' => 2, 'b' => 4], $merged->getSanitizedData());
        $this->assertCount(2, $merged->getModifications());
    }

    #[Test]
    public function testFieldModificationWasModified(): void
    {
        $changed = new FieldModification('x', 'rule', 'A', 'a');
        $unchanged = new FieldModification('x', 'rule', 'same', 'same');
        $this->assertTrue($changed->wasModified());
        $this->assertFalse($unchanged->wasModified());
    }
}
