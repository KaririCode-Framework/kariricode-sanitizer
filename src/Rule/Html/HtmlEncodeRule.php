<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Encodes HTML special characters for XSS prevention.
 *
 * Parameters: flags (int, default ENT_QUOTES|ENT_SUBSTITUTE), encoding (string, default 'UTF-8').
 */
final readonly class HtmlEncodeRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $flags = (int) $context->getParameter('flags', ENT_QUOTES | ENT_SUBSTITUTE);
        $encoding = (string) $context->getParameter('encoding', 'UTF-8');
        $doubleEncode = (bool) $context->getParameter('double_encode', true);

        return htmlspecialchars($value, $flags, $encoding, $doubleEncode);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.encode';
    }
}
