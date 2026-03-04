<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Date;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Normalizes a date string to a standard format.
 *
 * Parameters: from (string, default 'd/m/Y'), to (string, default 'Y-m-d').
 */
final readonly class NormalizeDateRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (!is_string($value) || trim($value) === '') {
            return $value;
        }

        $from = (string) $context->getParameter('from', 'd/m/Y');
        $to = (string) $context->getParameter('to', 'Y-m-d');

        $date = \DateTimeImmutable::createFromFormat($from, $value);

        if ($date === false) {
            return $value;
        }

        return $date->format($to);
    }

    #[\Override]
    public function getName(): string
    {
        return 'date.normalize';
    }
}
