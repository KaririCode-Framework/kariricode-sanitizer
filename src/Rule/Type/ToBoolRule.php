<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Type;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Casts to boolean. Recognizes '1','true','yes','on' as true, '0','false','no','off' as false.
 */
final readonly class ToBoolRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_string($value)) {
            return match (strtolower(trim($value))) {
                '1', 'true', 'yes', 'on' => true,
                '0', 'false', 'no', 'off', '' => false,
                default => $value,
            };
        }

        if (is_numeric($value)) {
            return (bool) $value;
        }

        return $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'type.to_bool';
    }
}
