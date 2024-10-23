<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\CharacterReplacementTrait;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

class MarkdownSanitizer extends AbstractSanitizerProcessor
{
    use WhitespaceSanitizerTrait;
    use CharacterReplacementTrait;

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->trimWhitespace($input);

        // Remove HTML tags, keeping Markdown intact
        $input = strip_tags($input);

        // Escape special Markdown characters
        return $this->replaceMultipleCharacters(
            $input,
            ['*' => '\\*', '_' => '\\_', '`' => '\\`', '#' => '\\#']
        );

        return $input;
    }
}
