<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait DomainSanitizerTrait
{
    protected function normalizeDomain(string $domain, array $commonTypos): string
    {
        foreach ($commonTypos as $correct => $typos) {
            foreach ($typos as $typo) {
                if (str_contains($domain, $typo)) {
                    return str_replace($typo, $correct, $domain);
                }
            }
        }

        return $domain;
    }

    protected function extractDomain(string $url): string
    {
        $url = trim($url);
        $url = preg_replace('/^https?:\/\//i', '', $url);
        $url = preg_replace('/\/.*$/', '', $url);

        return $url;
    }
}
