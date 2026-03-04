<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Decodes HTML entities back to characters.
 */
final readonly class HtmlDecodeRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $flags = (int) $context->getParameter('flags', ENT_QUOTES | ENT_SUBSTITUTE);
        $encoding = (string) $context->getParameter('encoding', 'UTF-8');

        return html_entity_decode($value, $flags, $encoding);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.decode';
    }
}
