<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class HtmlPurifierSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private const DEFAULT_ALLOWED_TAGS = ['p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li', 'a', 'img'];
    private const DEFAULT_ALLOWED_ATTRIBUTES = ['href' => ['a'], 'src' => ['img'], 'alt' => ['img']];
    private const DEFAULT_ENCODING = 'UTF-8';

    private array $allowedTags;
    private array $allowedAttributes;
    private string $encoding;

    public function __construct()
    {
        $this->allowedTags = self::DEFAULT_ALLOWED_TAGS;
        $this->allowedAttributes = self::DEFAULT_ALLOWED_ATTRIBUTES;
        $this->encoding = self::DEFAULT_ENCODING;
    }

    public function configure(array $options): void
    {
        $this->allowedTags = $options['allowedTags'] ?? $this->allowedTags;
        $this->allowedAttributes = $options['allowedAttributes'] ?? $this->allowedAttributes;
        $this->encoding = $options['encoding'] ?? $this->encoding;
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->sanitizeHtml($input);

        $dom = new \DOMDocument('1.0', $this->encoding);
        $this->loadHtmlToDom($dom, $input);
        $this->filterNodes($dom->getElementsByTagName('*'));

        return $this->cleanHtmlOutput($dom->saveHTML());
    }

    private function filterNodes(\DOMNodeList $nodes): void
    {
        for ($i = $nodes->length - 1; $i >= 0; --$i) {
            $node = $nodes->item($i);
            if (!$this->isAllowedTag($node->nodeName)) {
                $this->unwrapNode($node);
            } else {
                $this->filterAttributes($node);
            }
        }
    }

    private function filterAttributes(\DOMElement $element): void
    {
        for ($i = $element->attributes->length - 1; $i >= 0; --$i) {
            /** @var DOMNode */
            $attr = $element->attributes->item($i);
            if (!$this->isAllowedAttribute($element->nodeName, $attr->name)) {
                $element->removeAttribute($attr->name);
            }
        }
    }

    private function sanitizeHtml(string $html): string
    {
        $html = $this->removeScripts($html);

        return $this->removeComments($html);
    }

    private function loadHtmlToDom(\DOMDocument $dom, string $html): void
    {
        libxml_use_internal_errors(true);
        $isLoaded = $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $this->encoding), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $errors = libxml_get_errors();
        libxml_clear_errors();
    }

    private function cleanHtmlOutput(string $output): string
    {
        $output = preg_replace('/^<!DOCTYPE.+?>/', '', $output);
        $output = str_replace(['<html>', '</html>', '<body>', '</body>'], '', $output);

        return trim($output);
    }

    private function isAllowedTag(string $tagName): bool
    {
        return in_array(strtolower($tagName), $this->allowedTags, true);
    }

    private function isAllowedAttribute(string $elementName, string $attributeName): bool
    {
        return isset($this->allowedAttributes[strtolower($attributeName)])
               && in_array(strtolower($elementName), $this->allowedAttributes[strtolower($attributeName)], true);
    }

    private function unwrapNode(\DOMNode $node): void
    {
        $parent = $node->parentNode;
        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }
        $parent->removeChild($node);
    }

    private function removeScripts(string $html): string
    {
        return $this->removeElementsByTagName('script', $html);
    }

    private function removeComments(string $html): string
    {
        return $this->removeElementsByXPath('//comment()', $html);
    }

    private function removeElementsByTagName(string $tagName, string $html): string
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $this->encoding), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $elements = $dom->getElementsByTagName($tagName);
        while ($elements->length > 0) {
            $elements->item(0)->parentNode->removeChild($elements->item(0));
        }

        return $dom->saveHTML();
    }

    private function removeElementsByXPath(string $query, string $html): string
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', $this->encoding), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query($query) as $element) {
            $element->parentNode->removeChild($element);
        }

        return $dom->saveHTML();
    }
}
