<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Core;

use KaririCode\PropertyInspector\Contract\PropertyAttributeHandler;
use KaririCode\PropertyInspector\Contract\PropertyChangeApplier;
use KaririCode\PropertyInspector\Utility\PropertyAccessor;
use KaririCode\Sanitizer\Attribute\Sanitize;

/**
 * Collects #[Sanitize] rule definitions from each property
 * and writes sanitized values back to the object via PropertyAccessor.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.2.0 ARFA 1.3
 */
final class SanitizeAttributeHandler implements PropertyAttributeHandler, PropertyChangeApplier
{
    /** @var array<string, mixed> */
    private array $data = [];

    /** @var array<string, list<string|\KaririCode\Sanitizer\Contract\SanitizationRule|array{0: string|\KaririCode\Sanitizer\Contract\SanitizationRule, 1: array<string, mixed>}>> */
    private array $fieldRules = [];

    /** @var array<string, mixed> */
    private array $processedValues = [];

    #[\Override]
    public function handleAttribute(string $propertyName, object $attribute, mixed $value): mixed
    {
        if (! $attribute instanceof Sanitize) {
            return null;
        }

        $this->data[$propertyName] = $value;

        if (! isset($this->fieldRules[$propertyName])) {
            $this->fieldRules[$propertyName] = [];
        }

        $this->fieldRules[$propertyName] = [
            ...$this->fieldRules[$propertyName],
            ...$attribute->rules,
        ];

        return null;
    }

    #[\Override]
    public function getProcessedPropertyValues(): array
    {
        return $this->data;
    }

    #[\Override]
    public function getProcessingResultMessages(): array
    {
        return [];
    }

    #[\Override]
    public function getProcessingResultErrors(): array
    {
        return [];
    }

    /** @return array<string, list<string|\KaririCode\Sanitizer\Contract\SanitizationRule|array{0: string|\KaririCode\Sanitizer\Contract\SanitizationRule, 1: array<string, mixed>}>> */
    public function getFieldRules(): array
    {
        return $this->fieldRules;
    }

    /** @param array<string, mixed> $values */
    public function setProcessedValues(array $values): void
    {
        $this->processedValues = $values;
    }

    #[\Override]
    public function applyChanges(object $object): void
    {
        foreach ($this->processedValues as $property => $value) {
            try {
                new PropertyAccessor($object, $property)->setValue($value);
            } catch (\ReflectionException) {
                // Property doesn't exist — skip silently
            }
        }
    }
}
