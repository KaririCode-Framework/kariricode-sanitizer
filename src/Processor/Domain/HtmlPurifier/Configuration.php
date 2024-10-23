<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain\HtmlPurifier;

final class Configuration
{
    private const DEFAULT_ALLOWED_TAGS = [
        'p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li',
        'a', 'img', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
    ];
    private const DEFAULT_ALLOWED_ATTRIBUTES = [
        'href' => ['a'],
        'src' => ['img'],
        'alt' => ['img'],
    ];

    private array $allowedTags;
    private array $allowedAttributes;

    public function __construct()
    {
        $this->allowedTags = self::DEFAULT_ALLOWED_TAGS;
        $this->allowedAttributes = self::DEFAULT_ALLOWED_ATTRIBUTES;
    }

    public function configure(array $options): void
    {
        $this->allowedTags = $options['allowedTags'] ?? $this->allowedTags;
        $this->allowedAttributes = $options['allowedAttributes'] ?? $this->allowedAttributes;
    }

    public function getAllowedTags(): array
    {
        return $this->allowedTags;
    }

    public function getAllowedAttributes(): array
    {
        return $this->allowedAttributes;
    }
}
