<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor;

use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\Sanitizer\Contract\SanitizationResult as SanitizationResultContract;
use KaririCode\Sanitizer\Contract\SanitizationResultProcessor;
use KaririCode\Sanitizer\SanitizationResult;

class DefaultSanitizationResultProcessor implements SanitizationResultProcessor
{
    public function __construct(
        private SanitizationResultContract $result = new SanitizationResult()
    ) {
    }

    public function process(AttributeHandler $handler): SanitizationResult
    {
        $processedValues = $handler->getProcessedPropertyValues();
        $errors = $handler->getProcessingResultErrors();

        foreach ($processedValues as $property => $data) {
            $this->result->setSanitizedData($property, $data['value']);

            if (isset($errors[$property])) {
                $this->addPropertyErrors($this->result, $property, $errors[$property]);
            }
        }

        return $this->result;
    }

    private function addPropertyErrors(
        SanitizationResult $result,
        string $property,
        array $propertyErrors
    ): void {
        foreach ($propertyErrors as $error) {
            $result->addError($property, $error['errorKey'], $error['message']);
        }
    }
}
