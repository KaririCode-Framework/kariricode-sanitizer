<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Truncates a string to a maximum length.
 *
 * Parameters: max (int, default 255), suffix (string, default '...').
 */
final readonly class TruncateRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $maxRaw = $context->getParameter('max', 255);
        $max = \is_int($maxRaw) ? $maxRaw : 255;
        $suffixRaw = $context->getParameter('suffix', '...');
        $suffix = \is_string($suffixRaw) ? $suffixRaw : '...';

        if (mb_strlen($value, 'UTF-8') <= $max) {
            return $value;
        }

        return mb_substr($value, 0, $max - mb_strlen($suffix, 'UTF-8'), 'UTF-8') . $suffix;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.truncate';
    }
}
