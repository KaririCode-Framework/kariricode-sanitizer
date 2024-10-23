<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security\Filename;

final class FilenameParser
{
    public function __construct(
        private readonly ExtensionHandler $extensionHandler
    ) {
    }

    public function splitFilename(string $filename, bool $preserveExtension): array
    {
        if (!$preserveExtension) {
            return [$filename, ''];
        }

        $lastDotPosition = strrpos($filename, '.');
        if (false === $lastDotPosition) {
            return [$filename, ''];
        }

        $basename = substr($filename, 0, $lastDotPosition);
        $extension = substr($filename, $lastDotPosition);

        if ($this->shouldHandleCompoundExtension($filename, $basename)) {
            $previousDotPosition = strrpos($basename, '.');
            $basename = substr($filename, 0, $previousDotPosition);
            $extension = substr($filename, $previousDotPosition);
        }

        return [$basename, $extension];
    }

    private function shouldHandleCompoundExtension(string $filename, string $basename): bool
    {
        $previousDotPosition = strrpos($basename, '.');

        return false !== $previousDotPosition && $this->extensionHandler->isCompoundExtension($filename);
    }
}
