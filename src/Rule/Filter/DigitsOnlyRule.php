<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Filter;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Strips all non-digit characters from a string.
 */
final readonly class DigitsOnlyRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        return preg_replace('/\D/', '', $value) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'filter.digits_only';
    }
}
