<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;
use KaririCode\Sanitizer\Processor\Security\Filename\BasenameSanitizer;
use KaririCode\Sanitizer\Processor\Security\Filename\Configuration;
use KaririCode\Sanitizer\Processor\Security\Filename\ExtensionHandler;
use KaririCode\Sanitizer\Processor\Security\Filename\FilenameParser;
use KaririCode\Sanitizer\Trait\WhitespaceSanitizerTrait;

final class FilenameSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    use WhitespaceSanitizerTrait;

    private readonly Configuration $config;
    private ExtensionHandler $extensionHandler;
    private FilenameParser $filenameParser;
    private BasenameSanitizer $basenameSanitizer;

    public function __construct()
    {
        $this->config = new Configuration();
        $this->initializeDependencies();
    }

    public function configure(array $options): void
    {
        $this->config->configure($options);
        $this->initializeDependencies();
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->trimWhitespace($input);

        if ('' === $input) {
            return '';
        }

        [$basename, $extension] = $this->filenameParser->splitFilename(
            $input,
            $this->config->isPreserveExtension()
        );

        $sanitizedBasename = $this->basenameSanitizer->sanitize(
            $basename,
            $this->config->isPreserveExtension()
        );

        if ('' === $sanitizedBasename) {
            return '';
        }

        $sanitizedExtension = $extension ? $this->extensionHandler->sanitizeExtension($extension) : '';

        return $this->buildFinalFilename($sanitizedBasename, $sanitizedExtension);
    }

    private function initializeDependencies(): void
    {
        $this->extensionHandler = new ExtensionHandler(
            $this->config->isBlockDangerousExtensions(),
            $this->config->getAllowedExtensions()
        );

        $this->filenameParser = new FilenameParser($this->extensionHandler);

        $this->basenameSanitizer = new BasenameSanitizer(
            $this->config->getReplacement(),
            $this->config->isToLowerCase(),
            $this->config->getMaxLength()
        );
    }

    private function buildFinalFilename(string $basename, string $extension): string
    {
        $filename = $basename . $extension;

        if (strlen($filename) > $this->config->getMaxLength()) {
            $maxBasenameLength = $this->config->getMaxLength() - strlen($extension);
            if ($maxBasenameLength > 0) {
                $basename = substr($basename, 0, $maxBasenameLength);
                $filename = $basename . $extension;
            } else {
                $filename = substr($filename, 0, $this->config->getMaxLength());
            }
        }

        return $filename;
    }
}
