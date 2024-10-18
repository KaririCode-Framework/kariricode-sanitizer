<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests;

use KaririCode\Contract\Processor\Processor;
use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Sanitizer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SanitizerTest extends TestCase
{
    private Sanitizer $sanitizer;
    private ProcessorRegistry|MockObject $registry;

    protected function setUp(): void
    {
        $this->registry = $this->createMock(ProcessorRegistry::class);
        $this->sanitizer = new Sanitizer($this->registry);
    }

    public function testSanitizeProcessesObjectProperties(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim'])]
            public string $name = '  Walmir Silva  ';

            #[Sanitize(processors: ['email'])]
            public string $email = 'walmir.silva@example..com';
        };

        $trimProcessor = $this->createMock(Processor::class);
        $trimProcessor->expects($this->once())
            ->method('process')
            ->with('  Walmir Silva  ')
            ->willReturn('Walmir Silva');

        $emailProcessor = $this->createMock(Processor::class);
        $emailProcessor->expects($this->once())
            ->method('process')
            ->with('walmir.silva@example..com')
            ->willReturn('walmir.silva@example.com');

        $this->registry->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['sanitizer', 'trim', $trimProcessor],
                ['sanitizer', 'email', $emailProcessor],
            ]);

        $sanitizedValues = $this->sanitizer->sanitize($testObject);

        $this->assertSame('Walmir Silva', $testObject->name);
        $this->assertSame('walmir.silva@example.com', $testObject->email);
        $this->assertArrayHasKey('name', $sanitizedValues['sanitizedValues']);
        $this->assertArrayHasKey('email', $sanitizedValues['sanitizedValues']);
        $this->assertSame('Walmir Silva', $sanitizedValues['sanitizedValues']['name']['value']);
        $this->assertSame('walmir.silva@example.com', $sanitizedValues['sanitizedValues']['email']['value']);
    }

    public function testSanitizeHandlesNonProcessableAttributes(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim'])]
            public string $processable = '  trim me  ';

            public string $nonProcessable = 'leave me alone';
        };

        $trimProcessor = $this->createMock(Processor::class);
        $trimProcessor->expects($this->once())
            ->method('process')
            ->with('  trim me  ')
            ->willReturn('trim me');

        $this->registry->expects($this->once())
            ->method('get')
            ->with('sanitizer', 'trim')
            ->willReturn($trimProcessor);

        $sanitizedValues = $this->sanitizer->sanitize($testObject);

        $this->assertSame('trim me', $testObject->processable);
        $this->assertSame('leave me alone', $testObject->nonProcessable);
        $this->assertArrayHasKey('processable', $sanitizedValues['sanitizedValues']);
        $this->assertArrayNotHasKey('nonProcessable', $sanitizedValues['sanitizedValues']);
    }

    public function testSanitizeHandlesExceptionsAndUsesFallbackValue(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['problematic'], messages: ['fallback' => 'Processing failed'])]
            public string $problematic = 'cause problem';
        };

        $problematicProcessor = $this->createMock(Processor::class);
        $problematicProcessor->expects($this->once())
            ->method('process')
            ->willThrowException(new \Exception('Processing failed'));

        $this->registry->expects($this->once())
            ->method('get')
            ->with('sanitizer', 'problematic')
            ->willReturn($problematicProcessor);

        $sanitizedValues = $this->sanitizer->sanitize($testObject);

        $this->assertSame('cause problem', $testObject->problematic);
        $this->assertArrayNotHasKey('problematic', $sanitizedValues['sanitizedValues']);
    }

    public function testSanitizeHandlesPrivateAndProtectedProperties(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim'])]
            private string $privateProp = '  private  ';

            #[Sanitize(processors: ['trim'])]
            protected string $protectedProp = '  protected  ';

            public function getPrivateProp(): string
            {
                return $this->privateProp;
            }

            public function getProtectedProp(): string
            {
                return $this->protectedProp;
            }
        };

        $trimProcessor = $this->createMock(Processor::class);
        $trimProcessor->expects($this->exactly(2))
            ->method('process')
            ->willReturnMap([
                ['  private  ', 'private'],
                ['  protected  ', 'protected'],
            ]);

        $this->registry->expects($this->exactly(2))
            ->method('get')
            ->with('sanitizer', 'trim')
            ->willReturn($trimProcessor);

        $sanitizedValues = $this->sanitizer->sanitize($testObject);

        $this->assertSame('private', $testObject->getPrivateProp());
        $this->assertSame('protected', $testObject->getProtectedProp());
        $this->assertArrayHasKey('privateProp', $sanitizedValues['sanitizedValues']);
        $this->assertArrayHasKey('protectedProp', $sanitizedValues['sanitizedValues']);
    }

    public function testSanitizeHandlesMultipleProcessorsForSingleProperty(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim', 'uppercase'])]
            public string $multiProcessed = '  hello world  ';
        };

        $trimProcessor = $this->createMock(Processor::class);
        $trimProcessor->expects($this->once())
            ->method('process')
            ->with('  hello world  ')
            ->willReturn('hello world');

        $uppercaseProcessor = $this->createMock(Processor::class);
        $uppercaseProcessor->expects($this->once())
            ->method('process')
            ->with('hello world')
            ->willReturn('HELLO WORLD');

        $this->registry->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['sanitizer', 'trim', $trimProcessor],
                ['sanitizer', 'uppercase', $uppercaseProcessor],
            ]);

        $sanitizedValues = $this->sanitizer->sanitize($testObject);

        $this->assertSame('HELLO WORLD', $testObject->multiProcessed);
        $this->assertArrayHasKey('multiProcessed', $sanitizedValues['sanitizedValues']);
        $this->assertSame('HELLO WORLD', $sanitizedValues['sanitizedValues']['multiProcessed']['value']);
    }
}
