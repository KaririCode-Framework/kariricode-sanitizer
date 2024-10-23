<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Processor\Domain\HtmlPurifier\Cleaner;

final class OutputCleaner
{
    public function clean(string $output): string
    {
        // Remove any remaining HTML comments
        $output = preg_replace('/<!--.*?-->/s', '', $output);

        // Normalize whitespace
        $output = preg_replace('/^\s+|\s+$/m', '', $output);
        $output = preg_replace('/\s+/', ' ', $output);

        return trim($output);
    }
}
