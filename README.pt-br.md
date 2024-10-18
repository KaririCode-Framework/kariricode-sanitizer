# Framework KaririCode: Sanitizer Component

[![en](https://img.shields.io/badge/lang-en-red.svg)](README.md) [![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](README.pt-br.md)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white) ![PHPUnit](https://img.shields.io/badge/PHPUnit-3776AB?style=for-the-badge&logo=php&logoColor=white)

Um componente robusto e flexível de sanitização de dados para PHP, parte do Framework KaririCode. Utiliza processadores configuráveis e funções nativas para garantir a integridade e segurança dos dados em suas aplicações.

## Índice

- [Características](#características)
- [Instalação](#instalação)
- [Uso](#uso)
  - [Uso Básico](#uso-básico)
  - [Uso Avançado: Sanitização de Post de Blog](#uso-avançado-sanitização-de-post-de-blog)
- [Sanitizadores Disponíveis](#sanitizadores-disponíveis)
- [Configuração](#configuração)
- [Integração com Outros Componentes KaririCode](#integração-com-outros-componentes-kariricode)
- [Desenvolvimento e Testes](#desenvolvimento-e-testes)
- [Contribuindo](#contribuindo)
- [Licença](#licença)
- [Suporte e Comunidade](#suporte-e-comunidade)

## Características

- Sanitização flexível baseada em atributos para propriedades de objetos
- Conjunto abrangente de sanitizadores integrados para casos de uso comuns
- Fácil integração com outros componentes KaririCode
- Processadores configuráveis para lógica de sanitização personalizada
- Suporte a valores de fallback em caso de falhas na sanitização
- Arquitetura extensível permitindo sanitizadores personalizados
- Tratamento e relatório de erros robusto

## Instalação

Você pode instalar o componente Sanitizer via Composer:

```bash
composer require kariricode/sanitizer
```

### Requisitos

- PHP 8.3 ou superior
- Composer

## Uso

### Uso Básico

1. Defina sua classe de dados com atributos de sanitização:

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

class PerfilUsuario
{
    #[Sanitize(processors: ['trim', 'html_special_chars'])]
    private string $nome = '';

    #[Sanitize(processors: ['trim', 'normalize_line_breaks'])]
    private string $email = '';

    // Getters e setters...
}
```

2. Configure o sanitizador e use-o:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Sanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;

$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());

$sanitizer = new Sanitizer($registry);

$perfilUsuario = new PerfilUsuario();
$perfilUsuario->setNome("  João Silva  ");
$perfilUsuario->setEmail("joao.silva@exemplo.com\r\n");

$resultado = $sanitizer->sanitize($perfilUsuario);

echo $perfilUsuario->getNome(); // Saída: "João Silva"
echo $perfilUsuario->getEmail(); // Saída: "joao.silva@exemplo.com\n"

// Acesse os resultados da sanitização
print_r($resultado['sanitizedValues']);
print_r($resultado['messages']);
print_r($resultado['errors']);
```

### Uso Avançado: Sanitização de Post de Blog

Aqui está um exemplo mais abrangente demonstrando como usar o Sanitizer do KaririCode em um cenário do mundo real, como sanitizar o conteúdo de um post de blog:

```php
<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;
use KaririCode\Sanitizer\Processor\Domain\MarkdownSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\NormalizeLineBreaksSanitizer;
use KaririCode\Sanitizer\Processor\Input\StripTagsSanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Security\XssSanitizer;
use KaririCode\Sanitizer\Sanitizer;

class PostBlog
{
    #[Sanitize(
        processors: ['trim', 'html_special_chars', 'xss_sanitizer'],
        messages: [
            'trim' => 'Título foi aparado',
            'html_special_chars' => 'Caracteres especiais no título foram escapados',
            'xss_sanitizer' => 'Tentativa de XSS foi removida do título',
        ]
    )]
    private string $titulo = '';

    #[Sanitize(
        processors: ['trim', 'normalize_line_breaks'],
        messages: [
            'trim' => 'Slug foi aparado',
            'normalize_line_breaks' => 'Quebras de linha no slug foram normalizadas',
        ]
    )]
    private string $slug = '';

    #[Sanitize(
        processors: ['trim', 'markdown', 'html_purifier'],
        messages: [
            'trim' => 'Conteúdo foi aparado',
            'markdown' => 'Markdown no conteúdo foi processado',
            'html_purifier' => 'HTML no conteúdo foi purificado',
        ]
    )]
    private string $conteudo = '';

    #[Sanitize(
        processors: ['trim', 'strip_tags', 'html_special_chars'],
        messages: [
            'trim' => 'Nome do autor foi aparado',
            'strip_tags' => 'Tags HTML foram removidas do nome do autor',
            'html_special_chars' => 'Caracteres especiais no nome do autor foram escapados',
        ]
    )]
    private string $nomeAutor = '';

    // Getters e setters...
}

// Configurar o sanitizador
$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());
$registry->register('sanitizer', 'strip_tags', new StripTagsSanitizer());
$registry->register('sanitizer', 'markdown', new MarkdownSanitizer());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());

// Configurar HTML Purifier com configurações específicas para conteúdo de blog
$htmlPurifier = new HtmlPurifierSanitizer();
$htmlPurifier->configure([
    'allowedTags' => ['p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li', 'a', 'img', 'h2', 'h3', 'blockquote'],
    'allowedAttributes' => ['href' => ['a'], 'src' => ['img'], 'alt' => ['img']],
]);
$registry->register('sanitizer', 'html_purifier', $htmlPurifier);

$sanitizer = new Sanitizer($registry);

// Simulando submissão de formulário com dados potencialmente inseguros
$postBlog = new PostBlog();
$postBlog->setTitulo("  Explorando KaririCode: Um Framework PHP Moderno <script>alert('xss')</script>  ");
$postBlog->setSlug(" explorando-kariricode-um-framework-php-moderno \r\n");
$postBlog->setConteudo("
# Introdução

KaririCode é um framework PHP **poderoso** e _flexível_ projetado para desenvolvimento web moderno.

<script>alert('código malicioso');</script>

## Características Principais

1. Sanitização robusta
2. Roteamento eficiente
3. ORM poderoso

Confira nosso [site oficial](https://kariricode.org) para mais informações!

<img src=\"malicioso.jpg\" onerror=\"alert('xss')\" />
");
$postBlog->setNomeAutor("<b>João Silva</b> <script>alert('xss')</script>");

$resultado = $sanitizer->sanitize($postBlog);

// Acessar dados sanitizados
echo $postBlog->getTitulo(); // Título sanitizado
echo $postBlog->getConteudo(); // Conteúdo sanitizado

// Acessar detalhes da sanitização
print_r($resultado['sanitizedValues']);
print_r($resultado['messages']);
print_r($resultado['errors']);
```

Este exemplo demonstra como usar o Sanitizer do KaririCode para limpar e proteger dados de posts de blog, incluindo o tratamento de conteúdo Markdown, purificação de HTML e proteção contra ataques XSS.

## Sanitizadores Disponíveis

O componente Sanitizer fornece vários sanitizadores integrados:

### Sanitizadores de Entrada

- TrimSanitizer: Remove espaços em branco do início e fim de uma string
- HtmlSpecialCharsSanitizer: Converte caracteres especiais em entidades HTML
- NormalizeLineBreaksSanitizer: Padroniza quebras de linha entre diferentes sistemas operacionais
- StripTagsSanitizer: Remove tags HTML e PHP de uma string

### Sanitizadores de Domínio

- HtmlPurifierSanitizer: Sanitiza conteúdo HTML usando a biblioteca HTML Purifier
- JsonSanitizer: Valida e formata strings JSON
- MarkdownSanitizer: Sanitiza conteúdo Markdown

### Sanitizadores de Segurança

- FilenameSanitizer: Garante que nomes de arquivos sejam seguros para uso em sistemas de arquivos
- SqlInjectionSanitizer: Protege contra ataques de injeção SQL
- XssSanitizer: Previne ataques de Cross-Site Scripting (XSS)

Para informações detalhadas sobre cada sanitizador, incluindo opções de configuração e exemplos de uso, consulte a [documentação](https://kariricode.org/docs/sanitizer).

## Configuração

O componente Sanitizer pode ser configurado globalmente ou por sanitizador. Aqui está um exemplo de como configurar o `HtmlPurifierSanitizer`:

```php
use KaririCode\Sanitizer\Processor\Domain\HtmlPurifierSanitizer;

$htmlPurifier = new HtmlPurifierSanitizer();
$htmlPurifier->configure([
    'allowedTags' => ['p', 'br', 'strong', 'em'],
    'allowedAttributes' => ['href' => ['a'], 'src' => ['img']],
]);

$registry->register('sanitizer', 'html_purifier', $htmlPurifier);
```

Para opções de configuração global, consulte o construtor da classe `Sanitizer`.

## Integração com Outros Componentes KaririCode

O componente Sanitizer é projetado para funcionar perfeitamente com outros componentes KaririCode:

- **KaririCode\Contract**: Fornece interfaces e contratos para integração consistente de componentes.
- **KaririCode\ProcessorPipeline**: Utilizado para construir e executar pipelines de sanitização.
- **KaririCode\PropertyInspector**: Usado para analisar e processar propriedades de objetos com atributos de sanitização.

Exemplo de integração:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\ProcessorPipeline\ProcessorBuilder;
use KaririCode\PropertyInspector\AttributeAnalyzer;
use KaririCode\PropertyInspector\AttributeHandler;
use KaririCode\PropertyInspector\Utility\PropertyInspector;
use KaririCode\Sanitizer\Sanitizer;

$registry = new ProcessorRegistry();
// Registrar sanitizadores...

$builder = new ProcessorBuilder($registry);
$attributeHandler = new AttributeHandler('sanitizer', $builder);
$propertyInspector = new PropertyInspector(new AttributeAnalyzer(Sanitize::class));

$sanitizer = new Sanitizer($registry);
```

## Desenvolvimento e Testes

Para fins de desenvolvimento e testes, este pacote usa Docker e Docker Compose para garantir consistência em diferentes ambientes. Um Makefile é fornecido para conveniência.

### Pré-requisitos

- Docker
- Docker Compose
- Make (opcional, mas recomendado para execução mais fácil de comandos)

### Configuração de Desenvolvimento

1. Clone o repositório:

   ```bash
   git clone https://github.com/KaririCode-Framework/kariricode-sanitizer.git
   cd kariricode-sanitizer
   ```

2. Configure o ambiente:

   ```bash
   make setup-env
   ```

3. Inicie os containers Docker:

   ```bash
   make up
   ```

4. Instale as dependências:
   ```bash
   make composer-install
   ```

### Comandos Make Disponíveis

- `make up`: Inicia todos os serviços em segundo plano
- `make down`: Para e remove todos os containers
- `make build`: Constrói imagens Docker
- `make shell`: Acessa o shell do container PHP
- `make test`: Executa testes
- `make coverage`: Executa cobertura de testes com formatação visual
- `make cs-fix`: Executa PHP CS Fixer para corrigir o estilo de código
- `make quality`: Executa todos os comandos de qualidade (cs-check, test, security-check)

Para uma lista completa de comandos disponíveis, execute:

```bash
make help
```

## Contribuindo

Agradecemos contribuições para o componente Sanitizer do KaririCode! Aqui está como você pode contribuir:

1. Faça um fork do repositório
2. Crie um novo branch para sua feature ou correção de bug
3. Escreva testes para suas alterações
4. Implemente suas alterações
5. Execute a suite de testes e garanta que todos os testes passem
6. Envie um pull request com uma descrição clara de suas alterações

Por favor, leia nosso [Guia de Contribuição](CONTRIBUTING.md) para mais detalhes sobre nosso código de conduta e processo de desenvolvimento.

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte e Comunidade

- **Documentação**: [https://kariricode.org/docs/sanitizer](https://kariricode.org/docs/sanitizer)
- **Rastreador de Problemas**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-sanitizer/issues)
- **Fórum da Comunidade**: [Comunidade KaririCode Club](https://kariricode.club)
- **Stack Overflow**: Marque suas perguntas com `kariricode-sanitizer`

---

Construído com ❤️ pela equipe KaririCode. Capacitando desenvolvedores para criar aplicações PHP mais seguras e robustas.
