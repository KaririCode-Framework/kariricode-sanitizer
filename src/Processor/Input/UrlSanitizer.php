<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Input;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Trait\CharacterReplacementTrait;
use KaririCode\Sanitizer\Trait\UrlSanitizerTrait;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

class UrlSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use WhitespaceSanitizerTrait;
    use UrlSanitizerTrait;
    use CharacterReplacementTrait;

    private const VALID_PROTOCOLS = ['http://', 'https://', 'ftp://', 'sftp://'];

    private bool $enforceProtocol = true;
    private string $defaultProtocol = 'https://';
    private bool $removeTrailingSlash = true;

    public function configure(array $options): void
    {
        $this->enforceProtocol = $options['enforceProtocol'] ?? $this->enforceProtocol;
        $this->defaultProtocol = $options['defaultProtocol'] ?? $this->defaultProtocol;
        $this->removeTrailingSlash = $options['removeTrailingSlash'] ?? $this->removeTrailingSlash;
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $url = $this->trimWhitespace($input);

        if ($this->isEmpty($url)) {
            return '';
        }

        return $this->buildSanitizedUrl($url);
    }

    private function isEmpty(string $url): bool
    {
        return '' === $url;
    }

    private function buildSanitizedUrl(string $url): string
    {
        $url = $this->normalizeSlashes($url);
        $url = $this->ensureProtocol($url);

        return $this->finalizeUrl($url);
    }

    private function ensureProtocol(string $url): string
    {
        if ($this->needsProtocol($url)) {
            return $this->addDefaultProtocol($url);
        }

        return $url;
    }

    private function needsProtocol(string $url): bool
    {
        return $this->enforceProtocol && !$this->containsProtocol($url);
    }

    private function containsProtocol(string $url): bool
    {
        $lowercaseUrl = strtolower($url);

        foreach (self::VALID_PROTOCOLS as $protocol) {
            if (str_starts_with($lowercaseUrl, $protocol)) {
                return true;
            }
        }

        return false;
    }

    private function addDefaultProtocol(string $url): string
    {
        return $this->defaultProtocol . $url;
    }

    private function finalizeUrl(string $url): string
    {
        if (!$this->removeTrailingSlash) {
            return $url;
        }

        return rtrim($url, '/');
    }
}
