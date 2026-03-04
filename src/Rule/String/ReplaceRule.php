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
        if (!is_string($value)) {
            return $value;
        }

        $search = $context->getParameter('search', '');
        $replace = (string) $context->getParameter('replace', '');

        if ($search === '' || $search === null) {
            return $value;
        }

        return str_replace((string) $search, $replace, $value);
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.replace';
    }
}
