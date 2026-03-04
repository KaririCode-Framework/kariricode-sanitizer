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
        if (! \is_string($value)) {
            return $value;
        }

        $value = mb_strtolower(trim($value), 'UTF-8');

        /** @psalm-suppress RedundantCast — PHPStan needs (string) cast; Psalm disagrees on return type */
        return (string) filter_var($value, FILTER_SANITIZE_EMAIL);
    }

    #[\Override]
    public function getName(): string
    {
        return 'filter.email';
    }
}
