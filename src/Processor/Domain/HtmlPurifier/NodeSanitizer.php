<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain\HtmlPurifier;

final class NodeSanitizer
{
    public function __construct(
        private readonly Configuration $config
    ) {
    }

    public function sanitizeNode(\DOMNode $node): void
    {
        if (!$node->hasChildNodes()) {
            return;
        }

        $children = iterator_to_array($node->childNodes);

        foreach ($children as $child) {
            if ($child instanceof \DOMElement) {
                $tagName = strtolower($child->tagName);

                // First, recursively process the child nodes
                $this->sanitizeNode($child);

                // Then, check if the current tag is allowed
                if (!in_array($tagName, $this->config->getAllowedTags(), true)) {
                    $this->unwrapNode($node, $child);
                }
            }
        }
    }

    private function unwrapNode(\DOMNode $parent, \DOMElement $element): void
    {
        $fragment = $parent->ownerDocument->createDocumentFragment();

        while ($element->firstChild) {
            $fragment->appendChild($element->firstChild);
        }

        $parent->replaceChild($fragment, $element);
    }
}
