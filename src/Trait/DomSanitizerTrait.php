<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait DomSanitizerTrait
{
    protected function createDom(string $input, bool $wrapInRoot = true): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        // Disable automatic entity encoding
        $dom->substituteEntities = false;
        $dom->formatOutput = false;

        if ($wrapInRoot) {
            $safeInput = '<div id="temp-root">' . htmlspecialchars_decode($input, ENT_QUOTES | ENT_HTML5) . '</div>';
        } else {
            $safeInput = htmlspecialchars_decode($input, ENT_QUOTES | ENT_HTML5);
        }

        $dom->loadHTML(
            mb_convert_encoding($safeInput, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        return $dom;
    }

    protected function cleanDomOutput(\DOMDocument $dom): string
    {
        // Save without XML declaration and DOCTYPE
        $output = $dom->saveHTML();

        if (false === $output) {
            return '';
        }

        // Remove DOCTYPE, html and body tags
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
}
