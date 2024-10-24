<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Sanitizer\Sanitizer as SanitizerContract;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Contract\SanitizationResult;
use KaririCode\Sanitizer\Processor\DefaultSanitizationResultProcessor;

class Sanitizer implements SanitizerContract
{
    private const IDENTIFIER = 'sanitizer';

    private ProcessorBuilder $builder;

    public function __construct(
        private readonly ProcessorRegistry $registry
    ) {
        $this->builder = new ProcessorBuilder($this->registry);
    }

    public function sanitize(mixed $object): SanitizationResult
    {
        $attributeHandler = new AttributeHandler(self::IDENTIFIER, $this->builder);
        $propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Sanitize::class)
        );

        $propertyInspector->inspect($object, $attributeHandler);
        $attributeHandler->applyChanges($object);

        $resultProcessor = new DefaultSanitizationResultProcessor();

        return $resultProcessor->process($attributeHandler);
    }
}
