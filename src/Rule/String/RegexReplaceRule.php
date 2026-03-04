<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Regex-based search-and-replace.
 *
 * Parameters: pattern (string), replacement (string, default '').
 */
final readonly class RegexReplaceRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $pattern = (string) $context->getParameter('pattern', '');
        $replacement = (string) $context->getParameter('replacement', '');

        if ($pattern === '') {
            return $value;
        }

        return preg_replace($pattern, $replacement, $value) ?? $value;
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.regex_replace';
    }
}
