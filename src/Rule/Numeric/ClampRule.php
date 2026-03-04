<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Numeric;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Clamps a numeric value to [min, max] bounds.
 *
 * Parameters: min (int|float), max (int|float).
 */
final readonly class ClampRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_numeric($value)) {
            return $value;
        }

        $numeric = is_int($value) ? $value : (float) $value;
        $min = $context->getParameter('min');
        $max = $context->getParameter('max');

        if ($min !== null && is_numeric($min) && $numeric < $min) {
            return is_int($value) ? (int) $min : (float) $min;
        }

        if ($max !== null && is_numeric($max) && $numeric > $max) {
            return is_int($value) ? (int) $max : (float) $max;
        }

        return $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'numeric.clamp';
    }
}
