<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security\Filename;

final class Configuration
{
    private string $replacement = '_';
    private bool $preserveExtension = true;
    private int $maxLength = 255;
    private bool $toLowerCase = false;
    private array $allowedExtensions = [];
    private bool $blockDangerousExtensions = true;

    public function configure(array $options): void
    {
        $this->configureBasicOptions($options);
        $this->configureExtensionOptions($options);
        $this->configureSecurityOptions($options);
    }

    private function configureBasicOptions(array $options): void
    {
        if (isset($options['replacement']) && $this->isValidReplacement($options['replacement'])) {
            $this->replacement = $options['replacement'];
        }

        if (isset($options['preserveExtension'])) {
            $this->preserveExtension = (bool) $options['preserveExtension'];
        }

        if (isset($options['maxLength']) && $options['maxLength'] > 0) {
            $this->maxLength = (int) $options['maxLength'];
        }

        if (isset($options['toLowerCase'])) {
            $this->toLowerCase = (bool) $options['toLowerCase'];
        }
    }

    private function configureExtensionOptions(array $options): void
    {
        if (isset($options['allowedExtensions']) && is_array($options['allowedExtensions'])) {
            $this->allowedExtensions = array_map('strtolower', $options['allowedExtensions']);
        }
    }

    private function configureSecurityOptions(array $options): void
    {
        if (isset($options['blockDangerousExtensions'])) {
            $this->blockDangerousExtensions = (bool) $options['blockDangerousExtensions'];
        }
    }

    private function isValidReplacement(string $replacement): bool
    {
        return 1 === strlen($replacement) && 1 === preg_match('/^[\w\-]$/', $replacement);
    }

    public function getReplacement(): string
    {
        return $this->replacement;
    }

    public function isPreserveExtension(): bool
    {
        return $this->preserveExtension;
    }

    public function getMaxLength(): int
    {
        return $this->maxLength;
    }

    public function isToLowerCase(): bool
    {
        return $this->toLowerCase;
    }

    public function getAllowedExtensions(): array
    {
        return $this->allowedExtensions;
    }

    public function isBlockDangerousExtensions(): bool
    {
        return $this->blockDangerousExtensions;
    }
}
