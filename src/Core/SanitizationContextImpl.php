<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Core;

use KaririCode\Sanitizer\Contract\SanitizationContext;

/**
 * Immutable sanitization context implementation.
 *
 * @package KaririCode\Sanitizer\Core
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
final readonly class SanitizationContextImpl implements SanitizationContext
{
    /**
     * @param array<string, mixed> $rootData
     * @param array<string, mixed> $parameters
     */
    private function __construct(
        private string $fieldName,
        private array $rootData,
        private array $parameters,
    ) {
    }

    /** @param array<string, mixed> $rootData */
    public static function create(array $rootData): self
    {
        return new self('', $rootData, []);
    }

    #[\Override]
    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    #[\Override]
    public function getRootData(): array
    {
        return $this->rootData;
    }

    #[\Override]
    public function getParameter(string $key, mixed $default = null): mixed
    {
        return $this->parameters[$key] ?? $default;
    }

    #[\Override]
    public function getParameters(): array
    {
        return $this->parameters;
    }

    #[\Override]
    public function withField(string $field): static
    {
        return new self($field, $this->rootData, $this->parameters);
    }

    #[\Override]
    public function withParameters(array $parameters): static
    {
        return new self($this->fieldName, $this->rootData, [...$this->parameters, ...$parameters]);
    }
}
