<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait UrlSanitizerTrait
{
    private const VALID_PROTOCOLS = [
        'http://',
        'https://',
        'ftp://',
        'sftp://',
    ];

    protected function normalizeProtocol(string $url, string $defaultProtocol = 'https://'): string
    {
        $hasValidProtocol = $this->hasValidProtocol($url);

        if ($hasValidProtocol) {
            return $url;
        }

        return $defaultProtocol . ltrim($url, '/');
    }

    protected function normalizeSlashes(string $url): string
    {
        if (empty($url)) {
            return '';
        }

        $protocol = $this->extractProtocol($url);
        $path = $this->extractPath($url, $protocol);
        $normalizedPath = $this->normalizePath($path);

        if ($this->isPathEmpty($normalizedPath)) {
            return '/';
        }

        return $this->buildUrl($protocol, $normalizedPath);
    }

    private function hasValidProtocol(string $url): bool
    {
        $lowercaseUrl = strtolower($url);

        foreach (self::VALID_PROTOCOLS as $protocol) {
            if (str_starts_with($lowercaseUrl, $protocol)) {
                return true;
            }
        }

        return false;
    }

    private function extractProtocol(string $url): string
    {
        $matches = [];
        preg_match('/^[a-zA-Z]+:/', $url, $matches);

        return $matches[0] ?? '';
    }

    private function extractPath(string $url, string $protocol): string
    {
        if (empty($protocol)) {
            return $url;
        }

        $parts = explode($protocol, $url, 2);

        return $parts[1] ?? '';
    }

    private function normalizePath(string $path): string
    {
        return preg_replace('/\/+/', '/', $path);
    }

    private function isPathEmpty(string $path): bool
    {
        return '' === trim($path, '/');
    }

    private function buildUrl(string $protocol, string $path): string
    {
        if (empty($protocol)) {
            return $path;
        }

        return $protocol . '//' . ltrim($path, '/');
    }
}
