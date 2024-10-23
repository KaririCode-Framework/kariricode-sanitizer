<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait DomSanitizerTrait
{
    protected function createDom(string $input, bool $wrapInRoot = true): \DOMDocument
    {
        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);

        $content = $wrapInRoot ? '<div id="temp-root">' . $input . '</div>' : $input;
        $dom->loadHTML(
            mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        return $dom;
    }

    protected function cleanDomOutput(\DOMDocument $dom): string
    {
        $output = $dom->saveHTML();
        if (false === $output) {
            return '';
        }

        return preg_replace(
            [
                '/^<!DOCTYPE.*?>\n?/',
                '/<\/?html[^>]*>\n?/',
                '/<\/?body[^>]*>\n?/',
            ],
            '',
            trim($output)
        );
    }
}
