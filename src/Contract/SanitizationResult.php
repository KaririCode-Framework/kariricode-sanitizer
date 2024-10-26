<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Contract;

interface SanitizationResult
{
    public function isValid(): bool;

    public function getErrors(): array;

    public function getSanitizedData(): array;

    public function toArray(): array;
}
