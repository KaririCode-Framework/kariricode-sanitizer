<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Filter;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Strips all non-alphanumeric characters (Unicode-aware).
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class AlphanumericOnlyRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        return preg_replace('/[^\pL\pN]/u', '', $value) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'filter.alphanumeric_only';
    }
}
