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
        if (! is_numeric($value)) {
            return $value;
        }

        $precisionRaw = $context->getParameter('precision', 2);
        $precision = \is_int($precisionRaw) ? $precisionRaw : 2;
        $modeRaw = $context->getParameter('mode', 'round');
        $mode = \is_string($modeRaw) ? $modeRaw : 'round';
        $numeric = (float) $value;
        $multiplier = 10 ** $precision;

        return match ($mode) {
            'ceil' => ceil($numeric * (float) $multiplier) / (float) $multiplier,
            'floor' => floor($numeric * (float) $multiplier) / (float) $multiplier,
            default => round($numeric, $precision),
        };
    }

    #[\Override]
    public function getName(): string
    {
        return 'numeric.round';
    }
}
