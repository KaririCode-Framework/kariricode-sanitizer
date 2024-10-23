<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait HtmlCleanerTrait
{
    protected function removeScripts(string $input): string
    {
        $input = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
        $input = preg_replace('/\bon\w+\s*=\s*"[^"]*"/i', '', $input);

        return $input;
    }

    protected function removeComments(string $input): string
    {
        return preg_replace('/<!--.*?-->/s', '', $input);
    }

    protected function removeStyle(string $input): string
    {
        return preg_replace('#<style(.*?)>(.*?)</style>#is', '', $input);
    }
}
