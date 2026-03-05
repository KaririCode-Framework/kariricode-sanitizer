<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Html;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * URL-encodes a string value.
 *
 * Parameters: raw (bool, default false). If true, uses rawurlencode (RFC 3986).
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class UrlEncodeRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $rawParam = $context->getParameter('raw', false);
        $raw = \is_bool($rawParam) ? $rawParam : (bool) $rawParam;

        return $raw ? rawurlencode($value) : urlencode($value);
    }

    #[\Override]
    public function getName(): string
    {
        return 'html.url_encode';
    }
}
