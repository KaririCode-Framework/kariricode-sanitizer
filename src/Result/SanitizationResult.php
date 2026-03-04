<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Result;

/**
 * Result of a sanitization pass — contains sanitized data and modification log.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final class SanitizationResult
{
    /** @var list<FieldModification> */
    private array $modifications = [];

    /**
     * @param array<string, mixed> $originalData
     * @param array<string, mixed> $sanitizedData
     */
    public function __construct(
        private readonly array $originalData,
        private array $sanitizedData,
    ) {
    }

    /** @return array<string, mixed> */
    public function getOriginalData(): array
    {
        return $this->originalData;
    }

    /** @return array<string, mixed> */
    public function getSanitizedData(): array
    {
        return $this->sanitizedData;
    }

    public function get(string $field): mixed
    {
        return $this->sanitizedData[$field] ?? null;
    }

    public function wasModified(): bool
    {
        return $this->originalData !== $this->sanitizedData;
    }

    public function isFieldModified(string $field): bool
    {
        if (! \array_key_exists($field, $this->originalData)) {
            return \array_key_exists($field, $this->sanitizedData);
        }

        return ($this->originalData[$field] ?? null) !== ($this->sanitizedData[$field] ?? null);
    }

    /** @return list<string> */
    public function modifiedFields(): array
    {
        $fields = [];
        foreach ($this->sanitizedData as $field => $value) {
            if ($this->isFieldModified($field)) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    public function addModification(FieldModification $modification): void
    {
        $this->modifications[] = $modification;
    }

    public function setSanitizedValue(string $field, mixed $value): void
    {
        $this->sanitizedData[$field] = $value;
    }

    /** @return list<FieldModification> */
    public function getModifications(): array
    {
        return $this->modifications;
    }

    /** @return list<FieldModification> */
    public function modificationsFor(string $field): array
    {
        return array_values(array_filter(
            $this->modifications,
            static fn (FieldModification $m): bool => $m->field === $field,
        ));
    }

    public function modificationCount(): int
    {
        return \count(array_filter(
            $this->modifications,
            static fn (FieldModification $m): bool => $m->wasModified(),
        ));
    }

    public function merge(self $other): self
    {
        $merged = new self(
            [...$this->originalData, ...$other->originalData],
            [...$this->sanitizedData, ...$other->sanitizedData],
        );

        foreach ([...$this->modifications, ...$other->modifications] as $mod) {
            $merged->addModification($mod);
        }

        return $merged;
    }
}
