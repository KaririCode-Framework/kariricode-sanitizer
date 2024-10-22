<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class StripTagsSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private array $allowedTags = [];
    private bool $keepSafeAttributes = true;
    private array $safeAttributes = ['class', 'id', 'style'];

    public function configure(array $options): void
    {
        $this->configureAllowedTags($options);
        $this->configureSafeAttributes($options);
    }

    public function process(mixed $input): string
    {
        $this->guardAgainstNonString($input);

        return $this->stripHtmlTags($input);
    }

    private function stripHtmlTags(string $input): string
    {
        $input = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $input);
        $input = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $input);

        if ($this->hasNoAllowedTags()) {
            return strip_tags($input);
        }

        $dom = new \DOMDocument();

        $dom->loadHTML(
            mb_convert_encoding($input, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $this->sanitizeDom($dom);

        $output = $this->cleanDomOutput($dom);

        return $this->normalizeOutput($output);
    }

    private function sanitizeDom(\DOMDocument $dom): void
    {
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*');

        if (false === $nodes) {
            return;
        }

        foreach ($nodes as $node) {
            if (!in_array($node->nodeName, $this->allowedTags, true)) {
                if ($node->parentNode) {
                    $textContent = $dom->createTextNode($node->textContent);
                    $node->parentNode->replaceChild($textContent, $node);
                }
                continue;
            }

            $this->processNodeAttributes($node);
        }
    }

    private function processNodeAttributes(\DOMElement $node): void
    {
        if (!$this->keepSafeAttributes) {
            foreach (iterator_to_array($node->attributes) as $attribute) {
                $node->removeAttribute($attribute->nodeName);
            }

            return;
        }

        foreach (iterator_to_array($node->attributes) as $attribute) {
            if (!in_array($attribute->nodeName, $this->safeAttributes, true)) {
                $node->removeAttribute($attribute->nodeName);
            }
        }
    }

    private function cleanDomOutput(\DOMDocument $dom): string
    {
        $output = $dom->saveHTML();

        if (false === $output) {
            return '';
        }

        $output = preg_replace(
            [
                '/^<!DOCTYPE.*?>\n?/',
                '/<\/?html[^>]*>\n?/',
                '/<\/?body[^>]*>\n?/',
            ],
            '',
            $output
        );

        return trim($output);
    }

    private function normalizeOutput(string $output): string
    {
        $output = preg_replace('/\n+/', '', $output);
        $output = preg_replace('/>\s+</', '><', $output);

        return trim($output);
    }

    private function hasNoAllowedTags(): bool
    {
        return empty($this->allowedTags);
    }

    private function configureAllowedTags(array $options): void
    {
        if (isset($options['allowedTags']) && is_array($options['allowedTags'])) {
            $this->allowedTags = array_map('strtolower', $options['allowedTags']);
        }
    }

    private function configureSafeAttributes(array $options): void
    {
        $this->keepSafeAttributes = $options['keepSafeAttributes'] ?? $this->keepSafeAttributes;

        if (isset($options['safeAttributes']) && is_array($options['safeAttributes'])) {
            $this->safeAttributes = array_map('strtolower', $options['safeAttributes']);
        }
    }
}
