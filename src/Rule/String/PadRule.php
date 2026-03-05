<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Pads a string to a given length.
 *
 * Parameters: length (int), pad (string, default ' '), side ('left'|'right'|'both', default 'right').
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class PadRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $lengthRaw = $context->getParameter('length', 0);
        $length = \is_int($lengthRaw) ? $lengthRaw : 0;
        $padRaw = $context->getParameter('pad', ' ');
        $pad = \is_string($padRaw) ? $padRaw : ' ';
        $sideRaw = $context->getParameter('side', 'right');
        $side = \is_string($sideRaw) ? $sideRaw : 'right';

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
