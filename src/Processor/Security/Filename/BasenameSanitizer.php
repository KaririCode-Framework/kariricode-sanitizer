<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security\Filename;

final class BasenameSanitizer
{
    public function __construct(
        private readonly string $replacement = '_',
        private readonly bool $toLowerCase = false,
        private readonly int $maxLength = 255
    ) {
    }

    public function sanitize(string $basename, bool $preserveDots = true): string
    {
        if ($preserveDots) {
            return $this->sanitizePreservingDots($basename);
        }

        return $this->sanitizeWithoutDots($basename);
    }

    private function sanitizePreservingDots(string $basename): string
    {
        $parts = explode('.', $basename);
        $sanitizedParts = array_map(
            fn (string $part) => $this->sanitizePart($part),
            $parts
        );

        $result = implode('.', $sanitizedParts);

        return $this->finalizeBasename($result);
    }

    private function sanitizeWithoutDots(string $basename): string
    {
        $sanitized = preg_replace('/[^\p{L}\p{N}_\-\s]/u', $this->replacement, $basename);
        $sanitized = str_replace([' ', '.', '-'], $this->replacement, $sanitized);
        $sanitized = $this->normalizeReplacement($sanitized);

        return $this->finalizeBasename($sanitized);
    }

    private function sanitizePart(string $part): string
    {
        $sanitized = preg_replace('/[^\p{L}\p{N}_\-\s]/u', $this->replacement, $part);
        $sanitized = str_replace([' ', '-'], $this->replacement, $sanitized);

        return $this->normalizeReplacement($sanitized);
    }

    private function normalizeReplacement(string $input): string
    {
        $normalized = preg_replace(
            '/' . preg_quote($this->replacement, '/') . '{2,}/',
            $this->replacement,
            $input
        );

        return trim($normalized, $this->replacement);
    }

    private function finalizeBasename(string $basename): string
    {
        if ($this->toLowerCase) {
            $basename = strtolower($basename);
        }

        return $this->truncateBasename($basename);
    }

    private function truncateBasename(string $basename): string
    {
        if (strlen($basename) <= $this->maxLength) {
            return $basename;
        }

        if (false !== ($lastSpace = strrpos(substr($basename, 0, $this->maxLength), ' '))) {
            return substr($basename, 0, $lastSpace);
        }

        return substr($basename, 0, $this->maxLength);
    }
}
