<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security\Filename;

final class ExtensionHandler
{
    private const DANGEROUS_EXTENSIONS = [
        'php', 'phtml', 'phar', 'php3', 'php4', 'php5', 'php7', 'pht',
        'exe', 'bat', 'cmd', 'sh', 'cgi', 'pl', 'py',
        'asp', 'aspx', 'jsp', 'jspx',
    ];
    private const COMPOUND_EXTENSIONS = [
        '.tar.gz',
        '.tar.bz2',
        '.tar.xz',
    ];

    private bool $blockDangerousExtensions;
    private array $allowedExtensions;

    public function __construct(bool $blockDangerousExtensions = true, array $allowedExtensions = [])
    {
        $this->blockDangerousExtensions = $blockDangerousExtensions;
        $this->allowedExtensions = array_map('strtolower', $allowedExtensions);
    }

    public function sanitizeExtension(string $extension): string
    {
        if ('' === $extension) {
            return '';
        }

        $extension = strtolower($extension);
        $extension = ltrim($extension, '.');

        $parts = explode('.', $extension);
        $lastPart = end($parts);

        if ($this->isDangerousExtension($lastPart)) {
            return '';
        }

        if ($this->hasAllowedExtensionsRestriction() && !$this->isAllowedExtension($lastPart)) {
            return '';
        }

        return '.' . $extension;
    }

    public function isCompoundExtension(string $filename): bool
    {
        foreach (self::COMPOUND_EXTENSIONS as $ext) {
            if (str_ends_with(strtolower($filename), $ext)) {
                return true;
            }
        }

        return false;
    }

    private function isDangerousExtension(string $extension): bool
    {
        if (!$this->blockDangerousExtensions) {
            return false;
        }

        return in_array(strtolower($extension), self::DANGEROUS_EXTENSIONS, true);
    }

    private function hasAllowedExtensionsRestriction(): bool
    {
        return !empty($this->allowedExtensions);
    }

    private function isAllowedExtension(string $extension): bool
    {
        return in_array($extension, $this->allowedExtensions, true);
    }
}
