<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security;

use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class XssSanitizer extends AbstractSanitizerProcessor
{
    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
