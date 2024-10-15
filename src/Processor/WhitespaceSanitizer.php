<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor;

class WhitespaceSanitizer extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return preg_replace('/\s+/', ' ', trim($input));
    }
}
