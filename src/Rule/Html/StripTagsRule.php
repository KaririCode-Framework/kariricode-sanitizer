<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Strips HTML/PHP tags from a string.
 *
 * Parameters: allowed (string, e.g. '<b><i><a>').
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class StripTagsRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $allowedRaw = $context->getParameter('allowed', '');
        $allowed = \is_string($allowedRaw) ? $allowedRaw : '';

        return strip_tags($value, '' !== $allowed ? $allowed : null);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.strip_tags';
    }
}
