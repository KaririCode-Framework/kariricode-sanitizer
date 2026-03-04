<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Event;

use KaririCode\Sanitizer\Event\SanitizationCompletedEvent;
use KaririCode\Sanitizer\Event\SanitizationStartedEvent;
use KaririCode\Sanitizer\Result\SanitizationResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(SanitizationStartedEvent::class)]
#[CoversClass(SanitizationCompletedEvent::class)]
final class SanitizationEventsTest extends TestCase
{
    #[Test]
    public function testSanitizationStartedEventStoresFields(): void
    {
        $event = new SanitizationStartedEvent(['name', 'email'], 1.5);

        $this->assertSame(['name', 'email'], $event->fields);
        $this->assertSame(1.5, $event->timestamp);
    }

    #[Test]
    public function testSanitizationStartedEventDefaultTimestamp(): void
    {
        $event = new SanitizationStartedEvent(['name']);
        $this->assertSame(0.0, $event->timestamp);
    }

    #[Test]
    public function testSanitizationCompletedEventStoresResultAndDuration(): void
    {
        $result = new SanitizationResult(['name' => 'WALMIR'], ['name' => 'walmir']);
        $event = new SanitizationCompletedEvent($result, 12.5, 1_000_000.0);

        $this->assertSame($result, $event->result);
        $this->assertSame(12.5, $event->durationMs);
        $this->assertSame(1_000_000.0, $event->timestamp);
    }

    #[Test]
    public function testSanitizationCompletedEventDefaultTimestamp(): void
    {
        $result = new SanitizationResult([], []);
        $event = new SanitizationCompletedEvent($result, 0.5);
        $this->assertSame(0.0, $event->timestamp);
    }
}
