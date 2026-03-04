<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Integration;

use KaririCode\Sanitizer\Core\SanitizerEngine;
use KaririCode\Sanitizer\Result\SanitizationResult;

/**
 * Bridge for ARFA 1.3 reactive pipeline integration.
 *
 * @package KaririCode\Sanitizer\Integration
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
final readonly class ProcessorBridge
{
    /**
     * @param array<string, list<string|array>> $fieldRules
     */
    public function __construct(
        private SanitizerEngine $engine,
        private array $fieldRules,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     * @return array{data: array<string, mixed>, result: SanitizationResult}
     */
    public function process(array $data): array
    {
        $result = $this->engine->sanitize($data, $this->fieldRules);

        return [
            'data' => $result->getSanitizedData(),
            'result' => $result,
        ];
    }
}
