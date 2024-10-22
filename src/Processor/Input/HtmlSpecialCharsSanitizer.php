<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class HtmlSpecialCharsSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private int $flags = ENT_QUOTES | ENT_HTML5;
    private string $encoding = 'UTF-8';
    private bool $doubleEncode = true;

    public function configure(array $options): void
    {
        $this->flags = $options['flags'] ?? $this->flags;
        $this->encoding = $options['encoding'] ?? $this->encoding;
        $this->doubleEncode = $options['doubleEncode'] ?? $this->doubleEncode;
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return $this->escapeSpecialCharacters($input);
    }

    private function escapeSpecialCharacters(string $input): string
    {
        return htmlspecialchars(
            $input,
            $this->flags,
            $this->encoding,
            $this->doubleEncode
        );
    }
}
