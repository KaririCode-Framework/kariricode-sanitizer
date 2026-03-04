<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Collapses multiple whitespace characters into a single space.
 */
final readonly class NormalizeWhitespaceRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        return preg_replace('/\s+/', ' ', trim($value)) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.normalize_whitespace';
    }
}
