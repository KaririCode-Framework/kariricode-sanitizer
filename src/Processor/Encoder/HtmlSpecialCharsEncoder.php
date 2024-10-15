<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Encoder;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class HtmlSpecialCharsEncoder extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        return htmlspecialchars(
            $this->guardAgainstNonString($input),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
    }
}
