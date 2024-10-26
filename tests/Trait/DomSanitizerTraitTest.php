<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Trait;

use KaririCode\Sanitizer\Trait\DomSanitizerTrait;
use PHPUnit\Framework\TestCase;

final class DomSanitizerTraitTest extends TestCase
{
    private $traitObject;

    protected function setUp(): void
    {
        $this->traitObject = new class {
            use DomSanitizerTrait;

            public function callCreateDom(string $input, bool $wrapInRoot = true): \DOMDocument
            {
                return $this->createDom($input, $wrapInRoot);
            }

            public function callCleanDomOutput(\DOMDocument $dom): string
            {
                return $this->cleanDomOutput($dom);
            }
        };
    }

    public function testCreateDomWithWrapping(): void
    {
        $input = '<p>Test content</p>';
        $dom = $this->traitObject->callCreateDom($input);

        $this->assertInstanceOf(\DOMDocument::class, $dom);
        $root = $dom->getElementById('temp-root');
        $this->assertNotNull($root);
        $this->assertTrue($root->hasChildNodes());
    }

    public function testCreateDomWithoutWrapping(): void
    {
        $input = '<p>Test content</p>';
        $dom = $this->traitObject->callCreateDom($input, false);

        $this->assertInstanceOf(\DOMDocument::class, $dom);
        $root = $dom->getElementById('temp-root');
        $this->assertNull($root);
    }

    public function testCleanDomOutput(): void
    {
        $dom = new \DOMDocument();
        $dom->loadHTML('<!DOCTYPE html><html><body><p>Test</p></body></html>');

        $output = $this->traitObject->callCleanDomOutput($dom);
        $this->assertSame('<p>Test</p>', $output);
    }

    public function testCreateDomWithSpecialCharacters(): void
    {
        $input = '<p>Test & content</p>';
        $dom = $this->traitObject->callCreateDom($input);

        $this->assertInstanceOf(\DOMDocument::class, $dom);
        $html = $dom->saveHTML();
        $this->assertStringContainsString('Test &amp; content', $html);
    }

    public function testCreateDomWithNestedElements(): void
    {
        $input = '<div><p>Test <strong>content</strong></p></div>';
        $dom = $this->traitObject->callCreateDom($input);

        $this->assertInstanceOf(\DOMDocument::class, $dom);
        $root = $dom->getElementById('temp-root');
        $this->assertNotNull($root);
        $this->assertTrue($root->hasChildNodes());
    }

    public function testCleanDomOutputWithEmptyDocument(): void
    {
        $dom = new \DOMDocument();
        $output = $this->traitObject->callCleanDomOutput($dom);
        $this->assertSame('', $output);
    }
}
