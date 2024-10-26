<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor;

use KaririCode\Contract\Processor\Processor;
use KaririCode\Sanitizer\Exception\SanitizationException;

abstract class AbstractSanitizerProcessor implements Processor
{
    protected function guardAgainstNonString(mixed $input): string
    {
        if (!is_string($input)) {
            throw SanitizationException::invalidInput('string');
        }

        return $input;
    }

    abstract public function process(mixed $input): mixed;
}
