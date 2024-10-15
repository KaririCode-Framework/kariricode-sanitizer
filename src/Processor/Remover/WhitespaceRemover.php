<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Remover;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class WhitespaceRemover extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private string $charlist = " \t\n\r\0\x0B";

    public function configure(array $options): void
    {
        if (isset($options['charlist'])) {
            $this->charlist = $options['charlist'];
        }
    }

    public function process(mixed $input): string
    {
        return trim($this->guardAgainstNonString($input), $this->charlist);
    }
}
