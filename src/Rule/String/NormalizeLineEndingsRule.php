<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Normalizes line endings to Unix LF.
 */
final readonly class NormalizeLineEndingsRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        return \is_string($value)
            ? str_replace(["\r\n", "\r"], "\n", $value)
            : $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.normalize_line_endings';
    }
}
