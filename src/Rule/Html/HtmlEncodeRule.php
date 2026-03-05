<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Encodes HTML special characters for XSS prevention.
 *
 * Parameters: flags (int, default ENT_QUOTES|ENT_SUBSTITUTE), encoding (string, default 'UTF-8').
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class HtmlEncodeRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $flagsRaw = $context->getParameter('flags', ENT_QUOTES | ENT_SUBSTITUTE);
        $flags = \is_int($flagsRaw) ? $flagsRaw : ENT_QUOTES | ENT_SUBSTITUTE;
        $encodingRaw = $context->getParameter('encoding', 'UTF-8');
        $encoding = \is_string($encodingRaw) ? $encodingRaw : 'UTF-8';
        $doubleEncodeRaw = $context->getParameter('double_encode', true);
        $doubleEncode = \is_bool($doubleEncodeRaw) ? $doubleEncodeRaw : (bool) $doubleEncodeRaw;

        return htmlspecialchars($value, $flags, $encoding, $doubleEncode);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.encode';
    }
}
