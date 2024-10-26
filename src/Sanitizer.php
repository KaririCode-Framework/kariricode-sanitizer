<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Sanitizer\Sanitizer as SanitizerContract;
use KaririCode\ProcessorPipeline\Handler\ProcessorAttributeHandler;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Contract\SanitizationResult as SanitizationResultContract;
use KaririCode\Sanitizer\Result\SanitizationResult;

final class Sanitizer implements SanitizerContract
{
    private const IDENTIFIER = 'sanitizer';

    private readonly ProcessorBuilder $builder;

    public function __construct(
        private readonly ProcessorRegistry $registry
    ) {
        $this->builder = new ProcessorBuilder($this->registry);
    }

    public function sanitize(mixed $object): SanitizationResultContract
    {
        $attributeHandler = new ProcessorAttributeHandler(
            self::IDENTIFIER,
            $this->builder
        );

        $propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Sanitize::class)
        );

        /** @var PropertyAttributeHandler */
        $handler = $propertyInspector->inspect($object, $attributeHandler);
        $attributeHandler->applyChanges($object);

        return new SanitizationResult(
            $handler->getProcessingResults()
        );
    }
}
