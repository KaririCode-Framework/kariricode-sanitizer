<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait UrlSanitizerTrait
{
    protected function normalizeProtocol(string $url, string $defaultProtocol = 'https://'): string
    {
        $protocols = ['http://', 'https://', 'ftp://', 'sftp://'];
        foreach ($protocols as $protocol) {
            if (str_starts_with(strtolower($url), $protocol)) {
                return $url;
            }
        }

        return $defaultProtocol . ltrim($url, '/');
    }

    protected function normalizeSlashes(string $url): string
    {
        // Preserves protocol double slashes
        return preg_replace('/([^:])\/+/', '$1/', $url);
    }
}
