<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Removes non-printable control characters (preserves \n, \r, \t).
 */
final readonly class StripNonPrintableRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        // Remove control chars except \t (0x09), \n (0x0A), \r (0x0D)
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.strip_non_printable';
    }
}
