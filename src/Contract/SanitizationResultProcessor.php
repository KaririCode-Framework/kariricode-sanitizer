<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Contract;

use KaririCode\PropertyInspector\AttributeHandler;

interface SanitizationResultProcessor
{
    /**
     * Processa os resultados de sanitização após a execução dos processadores.
     *
     * @param AttributeHandler $handler O manipulador que contém os atributos processados
     *
     * @return SanitizationResult O resultado da sanitização, incluindo dados processados e erros
     */
    public function process(AttributeHandler $handler): SanitizationResult;
}
