<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Configuration;

/**
 * Immutable sanitizer engine configuration.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final readonly class SanitizerConfiguration
{
    public function __construct(
        /** Track which rules modified each field. */
        public bool $trackModifications = true,
        /** Preserve original data in result for diff. */
        public bool $preserveOriginal = true,
    ) {
    }
}
