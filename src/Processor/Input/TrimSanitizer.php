<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

class TrimSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use WhitespaceSanitizerTrait;

    private string $characterMask = " \t\n\r\0\x0B";
    private bool $trimLeft = true;
    private bool $trimRight = true;

    public function configure(array $options): void
    {
        if (isset($options['characterMask']) && is_string($options['characterMask'])) {
            $this->characterMask = $options['characterMask'];
        }

        $this->trimLeft = $options['trimLeft'] ?? $this->trimLeft;
        $this->trimRight = $options['trimRight'] ?? $this->trimRight;
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        if (!$this->shouldPerformTrim()) {
            return $input;
        }

        return $this->trimSelectedSides($input);
    }

    private function shouldPerformTrim(): bool
    {
        return $this->trimLeft || $this->trimRight;
    }

    private function trimSelectedSides(string $input): string
    {
        if ($this->shouldTrimBothSides()) {
            return trim($input, $this->characterMask);
        }

        if ($this->trimLeft) {
            $input = ltrim($input, $this->characterMask);
        }

        if ($this->trimRight) {
            $input = rtrim($input, $this->characterMask);
        }

        return $input;
    }

    private function shouldTrimBothSides(): bool
    {
        return $this->trimLeft && $this->trimRight;
    }
}
