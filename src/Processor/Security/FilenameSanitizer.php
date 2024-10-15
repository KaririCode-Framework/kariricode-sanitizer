<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class FilenameSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private string $replacement = '_';
    private bool $preserveExtension = true;
    private string $allowedChars = 'a-zA-Z0-9_\-\.';

    public function configure(array $options): void
    {
        if (isset($options['replacement']) && $this->isValidReplacement($options['replacement'])) {
            $this->replacement = $options['replacement'];
        }

        if (isset($options['preserveExtension'])) {
            $this->preserveExtension = (bool) $options['preserveExtension'];
        }

        if (isset($options['allowedChars']) && is_array($options['allowedChars'])) {
            $this->allowedChars = implode('', $options['allowedChars']);
        }
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);

        if ('' === $input) {
            return '';
        }

        [$filename, $extension] = $this->splitFilename($input);
        $sanitized = $this->sanitizeFilename($filename);

        return $sanitized . $extension;
    }

    private function isValidReplacement(string $replacement): bool
    {
        return 1 === preg_match('/^[\w\-]$/', $replacement);
    }

    private function splitFilename(string $input): array
    {
        if ($this->preserveExtension) {
            $pathInfo = pathinfo($input);
            $filename = $pathInfo['filename'] ?? '';
            $extension = isset($pathInfo['extension']) ? '.' . $pathInfo['extension'] : '';
        } else {
            $filename = preg_replace('/\.[^.]+$/', '', $input) ?: '';
            $extension = '';
        }

        return [$filename, $extension];
    }

    private function sanitizeFilename(string $filename): string
    {
        $sanitized = preg_replace("/[^{$this->allowedChars}]/", $this->replacement, $filename) ?? '';
        $sanitized = preg_replace('/' . preg_quote($this->replacement, '/') . '+/', $this->replacement, $sanitized) ?? '';

        return trim($sanitized, $this->replacement);
    }
}
