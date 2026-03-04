<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Type;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Wraps a non-array value into a single-element array.
 */
final readonly class ToArrayRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value === null) {
            return [];
        }

        return [$value];
    }

    #[\Override]
    public function getName(): string
    {
        return 'type.to_array';
    }
}
