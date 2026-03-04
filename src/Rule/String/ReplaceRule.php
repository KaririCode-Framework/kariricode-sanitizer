<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Simple string search-and-replace.
 *
 * Parameters: search (string), replace (string, default '').
 */
final readonly class ReplaceRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $searchRaw = $context->getParameter('search', '');
        if (! \is_string($searchRaw) || '' === $searchRaw) {
            return $value;
        }

        $replaceRaw = $context->getParameter('replace', '');
        $replace = \is_string($replaceRaw) ? $replaceRaw : '';

        return str_replace($searchRaw, $replace, $value);
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.replace';
    }
}
