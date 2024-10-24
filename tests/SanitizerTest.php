<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests;

use KaririCode\Contract\Processor\Processor;
use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Contract\SanitizationResult;
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

    private function createProcessorMock(mixed $input, mixed $output): Processor|MockObject
    {
        $processor = $this->createMock(Processor::class);
        $processor->method('process')
            ->with($input)
            ->willReturn($output);

        return $processor;
    }

    public function testSanitizeProcessesObjectProperties(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim'])]
            public string $name = '  Walmir Silva  ';

            #[Sanitize(processors: ['email'])]
            public string $email = 'walmir.silva@example..com';
        };

        // Configure TrimSanitizer mock to actually perform the trim operation
        $trimProcessor = $this->getMockBuilder(Processor::class)
            ->getMock();

        $trimProcessor->method('process')
            ->willReturnCallback(function ($input) {
                return trim($input); // Execute actual trim
            });

        $emailProcessor = $this->createMock(Processor::class);
        $emailProcessor->method('process')
            ->with('walmir.silva@example..com')
            ->willReturn('walmir.silva@example.com');

        // Ensure registry returns processors correctly
        $this->registry->method('get')
            ->willReturnCallback(function ($type, $name) use ($trimProcessor, $emailProcessor) {
                if ('trim' === $name) {
                    return $trimProcessor;
                }
                if ('email' === $name) {
                    return $emailProcessor;
                }

                return null;
            });

        $result = $this->sanitizer->sanitize($testObject);

        $this->assertSame('Walmir Silva', $testObject->name);
        $this->assertSame('walmir.silva@example.com', $testObject->email);
        $this->assertInstanceOf(SanitizationResult::class, $result);
    }

    public function testSanitizeHandlesNonProcessableAttributes(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim'])]
            public string $processable = '  trim me  ';

            public string $nonProcessable = 'leave me alone';
        };

        $trimProcessor = $this->createProcessorMock(
            '  trim me  ',
            'trim me'
        );

        $this->registry->method('get')
            ->with('sanitizer', 'trim')
            ->willReturn($trimProcessor);

        $result = $this->sanitizer->sanitize($testObject);

        $this->assertSame('trim me', $testObject->processable);
        $this->assertSame('leave me alone', $testObject->nonProcessable);
        $this->assertInstanceOf(SanitizationResult::class, $result);
    }

    public function testSanitizeHandlesExceptionsAndUsesFallbackValue(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['problematic'], messages: ['fallback' => 'Processing failed'])]
            public string $problematic = 'cause problem';
        };

        $problematicProcessor = $this->createMock(Processor::class);
        $problematicProcessor->method('process')
            ->willThrowException(new \Exception('Processing failed'));

        $this->registry->method('get')
            ->with('sanitizer', 'problematic')
            ->willReturn($problematicProcessor);

        $result = $this->sanitizer->sanitize($testObject);

        $this->assertSame('cause problem', $testObject->problematic);
        $this->assertInstanceOf(SanitizationResult::class, $result);
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

        // Create a single trim processor for both properties
        $trimProcessor = $this->createMock(Processor::class);
        $trimProcessor->method('process')
            ->willReturnMap([
                ['  private  ', 'private'],
                ['  protected  ', 'protected'],
            ]);

        $this->registry->method('get')
            ->with('sanitizer', 'trim')
            ->willReturn($trimProcessor);

        $result = $this->sanitizer->sanitize($testObject);

        $this->assertSame('private', $testObject->getPrivateProp());
        $this->assertSame('protected', $testObject->getProtectedProp());
        $this->assertInstanceOf(SanitizationResult::class, $result);
    }

    public function testSanitizeHandlesMultipleProcessorsForSingleProperty(): void
    {
        $testObject = new class {
            #[Sanitize(processors: ['trim', 'uppercase'])]
            public string $multiProcessed = '  hello world  ';
        };

        $trimProcessor = $this->createProcessorMock(
            '  hello world  ',
            'hello world'
        );

        $uppercaseProcessor = $this->createProcessorMock(
            'hello world',
            'HELLO WORLD'
        );

        $this->registry->method('get')
            ->willReturnMap([
                ['sanitizer', 'trim', $trimProcessor],
                ['sanitizer', 'uppercase', $uppercaseProcessor],
            ]);

        $result = $this->sanitizer->sanitize($testObject);

        $this->assertSame('HELLO WORLD', $testObject->multiProcessed);
        $this->assertInstanceOf(SanitizationResult::class, $result);
    }
}
