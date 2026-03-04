<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Strips HTML/PHP tags from a string.
 *
 * Parameters: allowed (string, e.g. '<b><i><a>').
 */
final readonly class StripTagsRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $allowed = (string) $context->getParameter('allowed', '');

        return strip_tags($value, $allowed !== '' ? $allowed : null);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.strip_tags';
    }
}
