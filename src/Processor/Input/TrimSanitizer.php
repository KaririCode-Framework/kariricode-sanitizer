<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class TrimSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private string $characterMask = " \t\n\r\0\x0B";

    public function configure(array $options): void
    {
        if (isset($options['characterMask'])) {
            $this->characterMask = $options['characterMask'];
        }
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return trim($input, $this->characterMask);
    }
}
