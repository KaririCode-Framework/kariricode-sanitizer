<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Result;

use KaririCode\ProcessorPipeline\Result\ProcessingResultCollection;
use KaririCode\Sanitizer\Contract\SanitizationResult as SanitizationResultcontract;

final class SanitizationResult implements SanitizationResultcontract
{
    public function __construct(
        private readonly ProcessingResultCollection $results
    ) {
    }

    public function isValid(): bool
    {
        return !$this->results->hasErrors();
    }

    public function getErrors(): array
    {
        return $this->results->getErrors();
    }

    public function getSanitizedData(): array
    {
        return $this->results->getProcessedData();
    }

    public function toArray(): array
    {
        return $this->results->toArray();
    }
}
