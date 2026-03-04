<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Filter;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Strips all non-letter characters (Unicode-aware via \pL).
 */
final readonly class AlphaOnlyRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        return preg_replace('/[^\pL]/u', '', $value) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'filter.alpha_only';
    }
}
