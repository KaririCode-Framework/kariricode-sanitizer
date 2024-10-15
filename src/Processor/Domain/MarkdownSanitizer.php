<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class MarkdownSanitizer extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        // Remove HTML tags, keeping Markdown intact
        $input = strip_tags($input);
        // Escape special Markdown characters
        $input = preg_replace('/([*_`#])/', '\\\\$1', $input);

        return $input;
    }
}
