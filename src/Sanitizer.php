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
use KaririCode\Sanitizer\Contract\SanitizationResultProcessor;
use KaririCode\Sanitizer\Processor\DefaultSanitizationResultProcessor;

class Sanitizer implements SanitizerContract
{
    private const IDENTIFIER = 'sanitizer';

    private ProcessorBuilder $builder;
    private PropertyInspector $propertyInspector;
    private AttributeHandler $attributeHandler;

    public function __construct(
        private readonly ProcessorRegistry $registry,
        private readonly SanitizationResultProcessor $resultProcessor = new DefaultSanitizationResultProcessor()
    ) {
        $this->builder = new ProcessorBuilder($this->registry);
        $this->attributeHandler = new AttributeHandler(self::IDENTIFIER, $this->builder);
        $this->propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Sanitize::class)
        );
    }

    public function sanitize(mixed $object): SanitizationResult
    {
        $this->propertyInspector->inspect($object, $this->attributeHandler);
        $this->attributeHandler->applyChanges($object);

        return $this->resultProcessor->process($this->attributeHandler);
    }
}
