<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Core;

use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Result\SanitizationResult;

/**
 * Sanitizes objects by reading #[Sanitize] attributes from properties.
 *
 * Uses kariricode/property-inspector for reflection caching and
 * attribute scanning — eliminates manual ReflectionClass loops.
 *
 * @author  Walmir Silva <walmir.silva@kariricode.org>
 *
 * @since   3.2.0 ARFA 1.3
 */
final readonly class AttributeSanitizer
{
    private PropertyInspector $inspector;

    public function __construct(private SanitizerEngine $engine)
    {
        $this->inspector = new PropertyInspector(
            new AttributeAnalyzer(Sanitize::class),
        );
    }

    public function sanitize(object $object): SanitizationResult
    {
        $handler = new SanitizeAttributeHandler();

        /** @var SanitizeAttributeHandler $handler */
        $handler = $this->inspector->inspect($object, $handler);

        $result = $this->engine->sanitize(
            $handler->getProcessedPropertyValues(),
            $handler->getFieldRules(),
        );

        $handler->setProcessedValues($result->getSanitizedData());
        $handler->applyChanges($object);

        return $result;
    }
}
