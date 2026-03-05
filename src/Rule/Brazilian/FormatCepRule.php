<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Brazilian;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Formats a CEP: 63100000 → 63100-000.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class FormatCepRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $digits = preg_replace('/\D/', '', $value) ?? '';

        if (8 !== \strlen($digits)) {
            return $value;
        }

        return substr($digits, 0, 5) . '-' . substr($digits, 5, 3);
    }

    #[\Override]
    public function getName(): string
    {
        return 'brazilian.format_cep';
    }
}
