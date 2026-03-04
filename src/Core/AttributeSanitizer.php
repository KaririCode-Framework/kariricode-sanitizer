<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Core;

use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Result\SanitizationResult;

/**
 * Sanitizes objects by reading #[Sanitize] attributes from properties.
 *
 * @package KaririCode\Sanitizer\Core
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 * @since   3.1.0 ARFA 1.3
 */
final readonly class AttributeSanitizer
{
    public function __construct(
        private SanitizerEngine $engine,
    ) {
    }

    public function sanitize(object $object): SanitizationResult
    {
        $ref = new \ReflectionClass($object);
        $data = [];
        $fieldRules = [];

        foreach ($ref->getProperties() as $property) {
            $attributes = $property->getAttributes(Sanitize::class);

            if ($attributes === []) {
                continue;
            }

            $field = $property->getName();
            $data[$field] = $this->extractValue($property, $object);

            $rules = [];
            foreach ($attributes as $attribute) {
                /** @var Sanitize $sanitize */
                $sanitize = $attribute->newInstance();
                $rules = [...$rules, ...$sanitize->rules];
            }

            $fieldRules[$field] = $rules;
        }

        $result = $this->engine->sanitize($data, $fieldRules);

        // Write sanitized values back to the object
        foreach ($result->getSanitizedData() as $field => $value) {
            if ($ref->hasProperty($field)) {
                $prop = $ref->getProperty($field);
                $prop->setValue($object, $value);
            }
        }

        return $result;
    }

    private function extractValue(\ReflectionProperty $property, object $object): mixed
    {
        try {
            return $property->getValue($object);
        } catch (\Error) {
            return null; // Uninitialized property
        }
    }
}
