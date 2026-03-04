<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Pads a string to a given length.
 *
 * Parameters: length (int), pad (string, default ' '), side ('left'|'right'|'both', default 'right').
 */
final readonly class PadRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $length = (int) $context->getParameter('length', 0);
        $pad = (string) $context->getParameter('pad', ' ');
        $side = (string) $context->getParameter('side', 'right');

        $padType = match ($side) {
            'left' => STR_PAD_LEFT,
            'both' => STR_PAD_BOTH,
            default => STR_PAD_RIGHT,
        };

        return str_pad($value, $length, $pad, $padType);
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.pad';
    }
}
