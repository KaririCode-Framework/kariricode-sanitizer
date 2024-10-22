<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class AlphanumericSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    /**
     * Special characters that can be optionally allowed.
     */
    private const SPECIAL_CHARS = [
        'space' => ' ',
        'underscore' => '_',
        'dash' => '-',
        'dot' => '.',
    ];

    private array $allowedSpecialChars = [];
    private bool $preserveCase = true;

    public function configure(array $options): void
    {
        $this->configureAllowedChars($options);
        $this->configureOptions($options);
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        $sanitized = $this->sanitizeString($input);

        return $this->applyCaseTransformation($sanitized);
    }

    private function configureAllowedChars(array $options): void
    {
        $this->allowedSpecialChars = [];

        foreach (self::SPECIAL_CHARS as $name => $char) {
            if (isset($options['allow' . ucfirst($name)]) && true === $options['allow' . ucfirst($name)]) {
                $this->allowedSpecialChars[] = preg_quote($char, '/');
            }
        }
    }

    private function configureOptions(array $options): void
    {
        $this->preserveCase = $options['preserveCase'] ?? $this->preserveCase;
    }

    private function sanitizeString(string $input): string
    {
        $allowedPattern = $this->buildAllowedPattern();

        return preg_replace($allowedPattern, '', $input);
    }

    private function buildAllowedPattern(): string
    {
        $allowed = $this->allowedSpecialChars;

        if (empty($allowed)) {
            return '/[^a-zA-Z0-9]/';
        }

        return '/[^a-zA-Z0-9' . implode('', $allowed) . ']/';
    }

    private function applyCaseTransformation(string $input): string
    {
        if (!$this->preserveCase) {
            return strtolower($input);
        }

        return $input;
    }
}
