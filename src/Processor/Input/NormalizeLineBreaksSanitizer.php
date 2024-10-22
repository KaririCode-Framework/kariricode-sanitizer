<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\CharacterReplacementTrait;

class NormalizeLineBreaksSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use CharacterReplacementTrait;

    private const LINE_ENDINGS = [
        'windows' => "\r\n",
        'mac' => "\r",
        'unix' => "\n",
    ];

    private string $targetLineEnding;

    public function __construct()
    {
        $this->targetLineEnding = self::LINE_ENDINGS['unix'];
    }

    public function configure(array $options): void
    {
        if (isset($options['lineEnding'])) {
            $this->setTargetLineEnding($options['lineEnding']);
        }
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        return $this->normalizeLineEndings($input);
    }

    private function normalizeLineEndings(string $input): string
    {
        $normalized = $this->convertToUnixLineEndings($input);

        if ($this->shouldConvertLineEndings()) {
            return $this->convertToTargetLineEndings($normalized);
        }

        return $normalized;
    }

    private function convertToUnixLineEndings(string $input): string
    {
        // First convert all Windows line endings to Unix
        $normalized = str_replace(self::LINE_ENDINGS['windows'], self::LINE_ENDINGS['unix'], $input);

        // Then convert any remaining Mac line endings to Unix
        return str_replace(self::LINE_ENDINGS['mac'], self::LINE_ENDINGS['unix'], $normalized);
    }

    private function shouldConvertLineEndings(): bool
    {
        return $this->targetLineEnding !== self::LINE_ENDINGS['unix'];
    }

    private function convertToTargetLineEndings(string $input): string
    {
        return str_replace(self::LINE_ENDINGS['unix'], $this->targetLineEnding, $input);
    }

    private function setTargetLineEnding(string $type): void
    {
        $type = strtolower($type);

        if (!isset(self::LINE_ENDINGS[$type])) {
            return;
        }

        $this->targetLineEnding = self::LINE_ENDINGS[$type];
    }
}
