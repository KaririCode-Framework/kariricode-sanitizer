<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Brazilian;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Formats a CPF number: 12345678901 → 123.456.789-01.
 *
 * If the input is already formatted, normalizes it.
 * If the digit count is not 11, returns as-is.
 */
final readonly class FormatCpfRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (11 !== \strlen($digits)) {
            return $value;
        }

        return substr($digits, 0, 3) . '.'
            . substr($digits, 3, 3) . '.'
            . substr($digits, 6, 3) . '-'
            . substr($digits, 9, 2);
    }

    #[\Override]
    public function getName(): string
    {
        return 'brazilian.format_cpf';
    }
}
