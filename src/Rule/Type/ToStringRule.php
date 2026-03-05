<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Type;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Casts scalars to string. Non-scalar values return as-is.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class ToStringRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (\is_string($value)) {
            return $value;
        }

        if (\is_scalar($value) || (\is_object($value) && method_exists($value, '__toString'))) {
            return (string) $value;
        }

        return $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'type.to_string';
    }
}
