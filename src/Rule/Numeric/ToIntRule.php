<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Numeric;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Casts a value to integer. Non-castable values return 0.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class ToIntRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (\is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        return $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'numeric.to_int';
    }
}
