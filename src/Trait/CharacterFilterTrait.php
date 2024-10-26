<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Trait;

trait CharacterFilterTrait
{
    protected function filterAllowedCharacters(string $input, string $allowed): string
    {
        $pattern = '';

        // Processa os intervalos (ex: a-z, 0-9)
        if (preg_match_all('/([a-z0-9])-([a-z0-9])/i', $allowed, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $start = $match[1];
                $end = $match[2];
                $pattern .= $start . '-' . $end;
                $allowed = str_replace($match[0], '', $allowed);
            }
        }

        // Adiciona caracteres individuais restantes
        $pattern .= preg_quote($allowed, '/');

        // Se não houver padrão, retorna string vazia
        if (empty($pattern)) {
            return '';
        }

        return preg_replace('/[^' . $pattern . ']/u', '', $input);
    }

    protected function keepOnlyAlphanumeric(string $input, array $additionalChars = []): string
    {
        $allowed = 'a-zA-Z0-9';

        if (!empty($additionalChars)) {
            $escaped = array_map(
                fn ($char) => preg_quote($char, '/'),
                $additionalChars
            );
            $allowed .= implode('', $escaped);
        }

        return preg_replace('/[^' . $allowed . ']/u', '', $input);
    }
}
