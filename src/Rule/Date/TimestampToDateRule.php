<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Rule\Date;

use KaririCode\Sanitizer\Contract\SanitizationContext;
use KaririCode\Sanitizer\Contract\SanitizationRule;

/**
 * Converts a Unix timestamp to a formatted date string.
 *
 * Parameters: format (string, default 'Y-m-d H:i:s'), timezone (string, default 'UTC').
 */
final readonly class TimestampToDateRule implements SanitizationRule
{
    #[\Override]
    public function sanitize(mixed $value, SanitizationContext $context): mixed
    {
        if (! is_numeric($value)) {
            return $value;
        }

        $formatRaw = $context->getParameter('format', 'Y-m-d H:i:s');
        $format = \is_string($formatRaw) ? $formatRaw : 'Y-m-d H:i:s';
        $timezoneRaw = $context->getParameter('timezone', 'UTC');
        $timezone = (\is_string($timezoneRaw) && '' !== $timezoneRaw) ? $timezoneRaw : 'UTC';

        try {
            $dt = new \DateTimeImmutable('@' . (int) $value)
                ->setTimezone(new \DateTimeZone($timezone));

            return $dt->format($format);
        } catch (\Exception) {
            return $value;
        }
    }

    #[\Override]
    public function getName(): string
    {
        return 'date.timestamp_to_date';
    }
}
