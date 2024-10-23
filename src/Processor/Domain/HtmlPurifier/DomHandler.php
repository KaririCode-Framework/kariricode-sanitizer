<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain\HtmlPurifier;

final class DomHandler
{
    private \DOMDocument $dom;
    private ?\DOMElement $root = null;

    public function __construct()
    {
        $this->dom = new \DOMDocument('1.0', 'UTF-8');
    }

    public function loadHtml(string $input): void
    {
        $wrappedInput = '<div id="temp-root">' . $input . '</div>';
        libxml_use_internal_errors(true);

        $this->dom->loadHTML(
            mb_convert_encoding($wrappedInput, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $this->root = $this->dom->getElementById('temp-root');
    }

    public function getRoot(): ?\DOMElement
    {
        return $this->root;
    }

    public function saveHtml(\DOMNode $node): string
    {
        return $this->dom->saveHTML($node) ?: '';
    }

    public function createDocumentFragment(): \DOMDocumentFragment
    {
        return $this->dom->createDocumentFragment();
    }
}
