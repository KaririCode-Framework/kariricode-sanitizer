<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

class JsonSanitizer extends AbstractSanitizerProcessor
{
    use WhitespaceSanitizerTrait;

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->trimWhitespace($input);

        $decoded = json_decode($input, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \InvalidArgumentException('Invalid JSON input');
        }

        return json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
