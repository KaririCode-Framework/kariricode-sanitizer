<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Core;

use KaririCode\Sanitizer\Configuration\SanitizerConfiguration;
use KaririCode\Sanitizer\Contract\RuleRegistry;
use KaririCode\Sanitizer\Contract\SanitizationRule;
use KaririCode\Sanitizer\Result\FieldModification;
use KaririCode\Sanitizer\Result\SanitizationResult;

/**
 * Central sanitization orchestrator.
 *
 * Applies per-field rule pipelines to input data, returning a
 * SanitizationResult with sanitized data and modification log.
 *
 * Supports dot-notation for nested data access.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.1.0 ARFA 1.3
 */
final class SanitizerEngine
{
    public function __construct(
        private readonly RuleRegistry $registry,
        private readonly ?SanitizerConfiguration $configuration = null,
    ) {
    }

    /**
     * Sanitize data against field-rule maps.
     *
     * @param array<string, mixed> $data Input data
     * @param array<string, list<string|SanitizationRule|array{0: string|SanitizationRule, 1: array<string, mixed>}>> $fieldRules Per-field rule definitions
     */
    public function sanitize(array $data, array $fieldRules): SanitizationResult
    {
        $config = $this->configuration ?? new SanitizerConfiguration();
        $result = new SanitizationResult($data, $data);
        $baseContext = SanitizationContextImpl::create($data);

        foreach ($fieldRules as $field => $rules) {
            $value = $this->resolveValue($data, $field);
            $fieldContext = $baseContext->withField($field);

            foreach ($rules as $ruleDefinition) {
                [$rule, $params] = $this->resolveRule($ruleDefinition);
                $ctx = [] !== $params ? $fieldContext->withParameters($params) : $fieldContext;

                $before = $value;
                $value = $rule->sanitize($value, $ctx);

                if ($config->trackModifications && $before !== $value) {
                    $result->addModification(new FieldModification(
                        $field,
                        $rule->getName(),
                        $before,
                        $value,
                    ));
                }
            }

            $this->setSanitizedValue($result, $field, $value);
        }

        return $result;
    }

    public function getConfiguration(): SanitizerConfiguration
    {
        return $this->configuration ?? new SanitizerConfiguration();
    }

    // ── Dot-Notation Resolution ───────────────────────────────────

    /**
     * @param array<string, mixed> $data
     */
    private function resolveValue(array $data, string $field): mixed
    {
        if (\array_key_exists($field, $data)) {
            return $data[$field];
        }

        $segments = explode('.', $field);
        $current = $data;

        foreach ($segments as $segment) {
            if (! \is_array($current) || ! \array_key_exists($segment, $current)) {
                return null;
            }
            $current = $current[$segment];
        }

        return $current;
    }

    private function setSanitizedValue(SanitizationResult $result, string $field, mixed $value): void
    {
        $result->setSanitizedValue($field, $value);
    }

    // ── Rule Resolution ───────────────────────────────────────────

    /**
     * @param string|array{0: SanitizationRule|string, 1: array<string, mixed>}|SanitizationRule $definition
     *
     * @return array{0: SanitizationRule, 1: array<string, mixed>}
     */
    private function resolveRule(string|array|SanitizationRule $definition): array
    {
        if ($definition instanceof SanitizationRule) {
            return [$definition, []];
        }

        if (\is_string($definition)) {
            return [$this->registry->resolve($definition), []];
        }

        // Array: [alias|rule, parameters]
        $ruleRef = $definition[0];
        $params = $definition[1] ?? [];

        $rule = $ruleRef instanceof SanitizationRule
            ? $ruleRef
            : $this->registry->resolve($ruleRef);

        return [$rule, $params];
    }
}
