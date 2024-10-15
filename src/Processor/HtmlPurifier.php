<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor;

use KaririCode\Contract\Processor\ConfigurableProcessor;

class HtmlPurifier extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private array $allowedTags = ['p', 'br', 'strong', 'em'];

    public function configure(array $options): void
    {
        if (isset($options['allowedTags'])) {
            $this->allowedTags = $options['allowedTags'];
        }
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return strip_tags($input, $this->allowedTags);
    }
}
