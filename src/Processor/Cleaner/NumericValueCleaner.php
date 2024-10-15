<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Cleaner;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class NumericValueCleaner extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        return filter_var(
            $input,
            FILTER_SANITIZE_NUMBER_FLOAT,
            FILTER_FLAG_ALLOW_FRACTION
        ) ?: '0';
    }
}
