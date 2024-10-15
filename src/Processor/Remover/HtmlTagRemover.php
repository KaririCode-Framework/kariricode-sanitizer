<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Remover;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class HtmlTagRemover extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private array $allowedTags = [];

    public function configure(array $options): void
    {
        if (isset($options['allowedTags'])) {
            $this->allowedTags = $options['allowedTags'];
        }
    }

    public function process(mixed $input): string
    {
        return strip_tags($this->guardAgainstNonString($input), $this->allowedTags);
    }
}
