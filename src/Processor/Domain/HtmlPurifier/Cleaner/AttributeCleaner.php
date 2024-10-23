<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Cleaner;

use KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Configuration;

final class AttributeCleaner
{
    public function __construct(
        private readonly Configuration $config
    ) {
    }

    public function cleanAttributes(\DOMElement $element): void
    {
        if (!in_array(strtolower($element->tagName), $this->config->getAllowedTags(), true)) {
            return;
        }

        $attributes = iterator_to_array($element->attributes);
        foreach ($attributes as $attr) {
            $attrName = $attr->name;
            $tagName = strtolower($element->tagName);

            if (!$this->isAttributeAllowed($attrName, $tagName)) {
                $element->removeAttribute($attrName);
            }
        }
    }

    private function isAttributeAllowed(string $attrName, string $tagName): bool
    {
        $allowedAttributes = $this->config->getAllowedAttributes();

        return isset($allowedAttributes[$attrName])
               && in_array($tagName, $allowedAttributes[$attrName], true);
    }
}
