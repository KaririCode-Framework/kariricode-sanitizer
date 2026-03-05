<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Converts a string to uppercase (mb-safe, UTF-8).
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class UpperCaseRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        return \is_string($value) ? mb_strtoupper($value, 'UTF-8') : $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.upper_case';
    }
}
