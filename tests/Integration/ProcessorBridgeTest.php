<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Integration;

use KaririCode\Sanitizer\Integration\ProcessorBridge;
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;
use KaririCode\Sanitizer\Result\SanitizationResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(ProcessorBridge::class)]
final class ProcessorBridgeTest extends TestCase
{
    #[Test]
    public function testProcessReturnsSanitizedDataAndResult(): void
    {
        $engine = new SanitizerServiceProvider()->createEngine();
        $bridge = new ProcessorBridge($engine, [
            'name' => ['trim', 'capitalize'],
        ]);

        $output = $bridge->process(['name' => '  walmir silva  ']);

        $this->assertArrayHasKey('data', $output);
        $this->assertArrayHasKey('result', $output);
        $this->assertSame('Walmir Silva', $output['data']['name']);
        $this->assertInstanceOf(SanitizationResult::class, $output['result']);
    }

    #[Test]
    public function testProcessWithEmptyDataReturnsEmptyArrays(): void
    {
        $engine = new SanitizerServiceProvider()->createEngine();
        $bridge = new ProcessorBridge($engine, []);

        $output = $bridge->process([]);

        $this->assertSame([], $output['data']);
        $this->assertInstanceOf(SanitizationResult::class, $output['result']);
    }
}
