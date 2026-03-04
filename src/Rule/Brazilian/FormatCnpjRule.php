<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Brazilian;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Formats a CNPJ number: 11222333000181 → 11.222.333/0001-81.
 */
final readonly class FormatCnpjRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (strlen($digits) !== 14) {
            return $value;
        }

        return substr($digits, 0, 2) . '.'
            . substr($digits, 2, 3) . '.'
            . substr($digits, 5, 3) . '/'
            . substr($digits, 8, 4) . '-'
            . substr($digits, 12, 2);
    }

    #[\Override]
    public function getName(): string
    {
        return 'brazilian.format_cnpj';
    }
}
