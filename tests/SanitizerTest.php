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
            #[Sanitize(sanitizers: ['trim'])]
            public string $name = '  John Doe  ';

            #[Sanitize(sanitizers: ['email'])]
            public string $email = 'john.doe@example..com';
        };

        $trimProcessor = $this->createMock(Processor::class);
        $trimProcessor->expects($this->once())
            ->method('process')
            ->with('  John Doe  ')
            ->willReturn('John Doe');

        $emailProcessor = $this->createMock(Processor::class);
        $emailProcessor->expects($this->once())
            ->method('process')
            ->with('john.doe@example..com')
            ->willReturn('john.doe@example.com');

        $this->registry->expects($this->exactly(2))
            ->method('get')
            ->willReturnMap([
                ['sanitizer', 'trim', $trimProcessor],
                ['sanitizer', 'email', $emailProcessor],
            ]);

        $sanitizedValues = $this->sanitizer->sanitize($testObject);

        $this->assertSame('John Doe', $testObject->name);
        $this->assertSame('john.doe@example.com', $testObject->email);
        $this->assertArrayHasKey('name', $sanitizedValues);
        $this->assertArrayHasKey('email', $sanitizedValues);
        $this->assertSame(['John Doe'], $sanitizedValues['name']);
        $this->assertSame(['john.doe@example.com'], $sanitizedValues['email']);
    }

    public function testSanitizeHandlesNonProcessableAttributes(): void
    {
        $testObject = new class {
            #[Sanitize(sanitizers: ['trim'])]
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
        $this->assertArrayHasKey('processable', $sanitizedValues);
        $this->assertArrayNotHasKey('nonProcessable', $sanitizedValues);
    }

    public function testSanitizeHandlesExceptionsAndUsesFallbackValue(): void
    {
        $testObject = new class {
            #[Sanitize(sanitizers: ['problematic'], fallbackValue: 'fallback')]
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
        $this->assertEmpty($sanitizedValues);
    }

    public function testSanitizeHandlesPrivateAndProtectedProperties(): void
    {
        $testObject = new class {
            #[Sanitize(sanitizers: ['trim'])]
            private string $privateProp = '  private  ';

            #[Sanitize(sanitizers: ['trim'])]
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
        $this->assertArrayHasKey('privateProp', $sanitizedValues);
        $this->assertArrayHasKey('protectedProp', $sanitizedValues);
    }

    public function testSanitizeHandlesMultipleProcessorsForSingleProperty(): void
    {
        $testObject = new class {
            #[Sanitize(sanitizers: ['trim', 'uppercase'])]
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
        $this->assertArrayHasKey('multiProcessed', $sanitizedValues);
        $this->assertSame(['HELLO WORLD'], $sanitizedValues['multiProcessed']);
    }
}
