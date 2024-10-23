<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer;

class SanitizationResult implements Contract\SanitizationResult
{
    private array $errors = [];
    private array $sanitizedData = [];

    public function addError(string $property, string $errorKey, string $message): void
    {
        if (!isset($this->errors[$property])) {
            $this->errors[$property] = [];
        }

        // Avoid adding duplicate errors
        foreach ($this->errors[$property] as $error) {
            if ($error['errorKey'] === $errorKey) {
                return;
            }
        }

        $this->errors[$property][] = [
            'errorKey' => $errorKey,
            'message' => $message,
        ];
    }

    public function setSanitizedData(string $property, mixed $value): void
    {
        $this->sanitizedData[$property] = $value;
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getSanitizedData(): array
    {
        return $this->sanitizedData;
    }

    public function toArray(): array
    {
        return [
            'isValid' => !$this->hasErrors(),
            'errors' => $this->errors,
            'sanitizedData' => $this->sanitizedData,
        ];
    }
}
