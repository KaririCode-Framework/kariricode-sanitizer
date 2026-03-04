<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Strips tags and decodes entities — aggressive XSS cleanup.
 *
 * Applies: strip_tags → html_entity_decode → trim.
 * Parameters: allowed (string, tags to preserve).
 */
final readonly class HtmlPurifyRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $allowed = (string) $context->getParameter('allowed', '');
        $value = strip_tags($value, $allowed !== '' ? $allowed : null);
        $value = html_entity_decode($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return trim($value);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.purify';
    }
}
