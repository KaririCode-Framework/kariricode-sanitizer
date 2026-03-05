<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Capitalizes the first letter of each word (mb-safe).
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class CapitalizeRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        return \is_string($value) ? mb_convert_case($value, MB_CASE_TITLE, 'UTF-8') : $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.capitalize';
    }
}
