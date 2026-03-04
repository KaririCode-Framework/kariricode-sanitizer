<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\String;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

final readonly class TrimRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! \is_string($value)) {
            return $value;
        }

        $characters = $context->getParameter('characters', " \t\n\r\0\x0B");
        $characters = \is_string($characters) ? $characters : " \t\n\r\0\x0B";

        return trim($value, $characters);
    }

    #[\Override]
    public function getName(): string
    {
        return 'string.trim';
    }
}
