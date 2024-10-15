<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class NormalizeLineBreaksSanitizer extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return str_replace(["\r\n", "\r"], "\n", $input);
    }
}
