<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Cleaner;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class EmailAddressCleaner extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        return filter_var(
            $this->guardAgainstNonString($input),
            FILTER_SANITIZE_EMAIL
        ) ?: '';
    }
}
