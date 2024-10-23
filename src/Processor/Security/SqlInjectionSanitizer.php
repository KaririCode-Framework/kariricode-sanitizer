<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Security;

use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

final class SqlInjectionSanitizer extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    // private const SQL_DETECTION_PATTERN = '/\b(SELECT|INSERT|UPDATE|DELETE|UNION|DROP|TRUNCATE|ALTER)\s.+\s(FROM|INTO|TABLE)\b/i';
    private const SUSPICIOUS_PATTERNS = [
        '/--.*$/m' => '',  // Remove single-line comments
        '/\/\*.*?\*\//s' => '',  // Remove multi-line comments
        '/;/' => '',  // Remove semicolons completamente
        '/\s+/' => ' ',  // Normalize whitespace
    ];

    private array $escapeMap = [
        "\x00" => '\\0',
        "\n" => '\\n',
        "\r" => '\\r',
        '\\' => '\\\\',
        "'" => "\\'",
        '"' => '\\"',
        "\x1a" => '\\Z',
    ];

    public function configure(array $options): void
    {
        if (isset($options['escapeMap']) && is_array($options['escapeMap'])) {
            $this->escapeMap = array_merge($this->escapeMap, $options['escapeMap']);
        }
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        $input = $this->removeSuspiciousPatterns($input);

        return $this->escapeString($input);
    }

    private function removeSuspiciousPatterns(string $input): string
    {
        return preg_replace(array_keys(self::SUSPICIOUS_PATTERNS), array_values(self::SUSPICIOUS_PATTERNS), $input);
    }

    private function escapeString(string $input): string
    {
        return strtr($input, $this->escapeMap);
    }
}
