<?php

namespace KaririCode\Sanitizer;

use KaririCode\Contract\Processor\ProcessorRegistry;
use KaririCode\Contract\Sanitizer\Sanitizer as SanitizerContract;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Attribute\Sanitize;

class Sanitizer implements SanitizerContract
{
    private const IDENTIFIER = 'sanitizer';

    private ProcessorBuilder $builder;
    private PropertyInspector $propertyInspector;
    private AttributeHandler $attributeHandler;

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
        $this->propertyInspector->inspect($object, $this->attributeHandler);
        $this->attributeHandler->applyChanges($object);

        return [
            'sanitizedValues' => $this->attributeHandler->getProcessedValues(),
            'messages' => $this->attributeHandler->getProcessingMessages(),
            'errors' => $this->attributeHandler->getProcessingErrors(),
            'object' => $object,
        ];
    }
}
