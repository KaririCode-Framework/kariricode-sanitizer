<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Cleaner;

final class InputCleaner
{
    public function clean(string $input): string
    {
        // Remove scripts and inline events
        $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
        $input = preg_replace('/\bon\w+\s*=\s*"[^"]*"/i', '', $input);
        $input = preg_replace('/<!--.*?-->/s', '', $input);

        return $input;
    }
}
