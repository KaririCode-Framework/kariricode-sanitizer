<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Contract;

interface SanitizationResult
{
    public function addError(string $property, string $errorKey, string $message): void;

    public function setSanitizedData(string $property, mixed $value): void;

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function getSanitizedData(): array;

    public function toArray(): array;
}
