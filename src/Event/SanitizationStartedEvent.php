<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Event;

/**
 * @package KaririCode\Sanitizer\Event
 * @since   3.1.0 ARFA 1.3
 */
final readonly class SanitizationStartedEvent
{
    /** @param list<string> $fields */
    public function __construct(
        public array $fields,
        public float $timestamp = 0,
    ) {
    }
}
