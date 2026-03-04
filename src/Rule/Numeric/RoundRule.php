<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Numeric;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Rounds a numeric value to specified decimal places.
 *
 * Parameters: precision (int, default 2), mode ('round'|'ceil'|'floor', default 'round').
 */
final readonly class RoundRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_numeric($value)) {
            return $value;
        }

        $precision = (int) $context->getParameter('precision', 2);
        $mode = (string) $context->getParameter('mode', 'round');
        $numeric = (float) $value;

        return match ($mode) {
            'ceil' => ceil($numeric * (10 ** $precision)) / (10 ** $precision),
            'floor' => floor($numeric * (10 ** $precision)) / (10 ** $precision),
            default => round($numeric, $precision),
        };
    }

    #[\Override]
    public function getName(): string
    {
        return 'numeric.round';
    }
}
