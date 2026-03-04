<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Filter;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Sanitizes an email address: trims, lowercases, removes illegal chars.
 */
final readonly class EmailFilterRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = mb_strtolower(trim($value), 'UTF-8');
        $filtered = filter_var($value, FILTER_SANITIZE_EMAIL);

        return $filtered !== false ? $filtered : $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'filter.email';
    }
}
