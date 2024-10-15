<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Sanitizer\Sanitizer as SanitizerContract;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Contract\PropertyAttributeHandler;
use KaririCode\PropertyInspector\Contract\PropertyChangeApplier;
use KaririCode\PropertyInspector\Exception\PropertyInspectionException;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Attribute\Sanitize;

class Sanitizer implements SanitizerContract
{
    private const IDENTIFIER = 'sanitizer';

    private ProcessorBuilder $builder;
    private PropertyInspector $propertyInspector;
    private PropertyAttributeHandler&PropertyChangeApplier $attributeHandler;

    public function __construct(private readonly ProcessorRegistry $registry)
    {
        $this->builder = new ProcessorBuilder($this->registry);
        $this->attributeHandler = new AttributeHandler(self::IDENTIFIER, $this->builder);
        $this->propertyInspector = new PropertyInspector(
            new AttributeAnalyzer(Sanitize::class)
        );
    }

    public function sanitize(mixed $object): array
    {
        try {
            $sanitizedValues = $this->propertyInspector->inspect($object, $this->attributeHandler);
            $this->attributeHandler->applyChanges($object);

            return $sanitizedValues;
        } catch (PropertyInspectionException $e) {
            return [];
        }
    }
}
