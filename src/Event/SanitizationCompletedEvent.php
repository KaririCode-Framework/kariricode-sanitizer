<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Event;

use KaririCode\Sanitizer\Result\SanitizationResult;

/**
 * @since   3.1.0 ARFA 1.3
 */
final readonly class SanitizationCompletedEvent
{
    public function __construct(
        public SanitizationResult $result,
        public float $durationMs,
        public float $timestamp = 0,
    ) {
    }
}
