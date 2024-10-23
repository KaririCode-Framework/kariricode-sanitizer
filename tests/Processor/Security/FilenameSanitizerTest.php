<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Processor\Security;

use KaririCode\Sanitizer\Exception\SanitizationException;
use KaririCode\Sanitizer\Processor\Security\FilenameSanitizer;
use PHPUnit\Framework\TestCase;

final class FilenameSanitizerTest extends TestCase
{
    private FilenameSanitizer $sanitizer;

    protected function setUp(): void
    {
        $this->sanitizer = new FilenameSanitizer();
    }

    public function testBasicFilenameSanitization(): void
    {
        $input = 'file@name!.txt';
        $expected = 'file_name.txt';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testSanitizationWithoutExtension(): void
    {
        $this->sanitizer->configure(['preserveExtension' => false]);
        $input = 'file@name!.txt';
        $expected = 'file_name_txt';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testCustomReplacementCharacter(): void
    {
        $this->sanitizer->configure(['replacement' => '-']);
        $input = 'file@name!.txt';
        $expected = 'file-name.txt';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testInvalidReplacementCharacter(): void
    {
        $this->sanitizer->configure(['replacement' => '*']);
        $input = 'file@name!.txt';
        $expected = 'file_name.txt';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testAllowedCharacters(): void
    {
        $this->sanitizer->configure(['allowedChars' => ['a-z', 'A-Z', '0-9']]);
        $input = 'file_name-123.txt';
        $expected = 'file_name_123.txt';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testCustomAllowedCharacters(): void
    {
        $this->sanitizer->configure(['allowedChars' => ['a-z', 'A-Z', '0-9', '_']]);
        $input = 'file@name!.txt';
        $expected = 'file_name.txt';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testEmptyFilenameReturnsEmptyString(): void
    {
        $input = '';
        $expected = '';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testFilenameWithoutExtension(): void
    {
        $input = 'filename@!with_no_extension';
        $expected = 'filename_with_no_extension';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testFilenameWithMultipleExtensions(): void
    {
        $input = 'file.name@!.tar.gz';
        $expected = 'file.name.tar.gz';
        $this->assertSame($expected, $this->sanitizer->process($input));
    }

    public function testNonStringInputThrowsException(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->sanitizer->process(12345);
    }

    public function testObjectInputThrowsException(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->sanitizer->process(new \stdClass());
    }

    public function testArrayInputThrowsException(): void
    {
        $this->expectException(SanitizationException::class);
        $this->expectExceptionMessage('Input must be a string');
        $this->sanitizer->process(['invalid', 'input']);
    }
}
