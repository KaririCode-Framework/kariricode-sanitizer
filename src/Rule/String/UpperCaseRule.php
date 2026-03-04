<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

final readonly class UpperCaseRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        return is_string($value) ? mb_strtoupper($value, 'UTF-8') : $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.upper_case';
    }
}
