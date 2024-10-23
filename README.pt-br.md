# KaririCode Framework: Componente Sanitizador

Um componente robusto e flexível de sanitização de dados para PHP, parte do KaririCode Framework. Utiliza processadores configuráveis e funções nativas para garantir a integridade e segurança dos dados em suas aplicações.

## Índice

- [Funcionalidades](#funcionalidades)
- [Instalação](#instalacao)
- [Uso](#uso)
  - [Uso Básico](#uso-basico)
  - [Uso Avançado: Sanitização de Postagem de Blog](#uso-avancado-sanitizacao-de-postagem-de-blog)
- [Sanitizadores Disponíveis](#sanitizadores-disponiveis)
  - [Sanitizadores de Entrada](#sanitizadores-de-entrada)
  - [Sanitizadores de Domínio](#sanitizadores-de-dominio)
  - [Sanitizadores de Segurança](#sanitizadores-de-seguranca)
- [Configuração](#configuracao)
- [Integração com Outros Componentes do KaririCode](#integracao-com-outros-componentes-do-kariricode)
- [Desenvolvimento e Testes](#desenvolvimento-e-testes)
- [Contribuindo](#contribuindo)
- [Licença](#licenca)
- [Suporte e Comunidade](#suporte-e-comunidade)

## Funcionalidades

- Sanitização flexível baseada em atributos para propriedades de objetos
- Conjunto abrangente de sanitizadores para casos de uso comuns
- Fácil integração com outros componentes do KaririCode
- Processadores configuráveis para lógica de sanitização personalizada
- Suporte para valores de fallback em caso de falhas de sanitização
- Arquitetura extensível permitindo sanitizadores personalizados
- Tratamento de erros robusto e relatórios detalhados
- Pipelines de sanitização encadeáveis para transformações complexas de dados
- Suporte nativo para múltiplas codificações de caracteres
- Proteção contra ataques XSS e injeção de SQL

## Instalação

Você pode instalar o componente Sanitizer via Composer:

```bash
composer require kariricode/sanitizer
```

### Requisitos

- PHP 8.3 ou superior
- Composer
- Extensões: `ext-mbstring`, `ext-dom`, `ext-libxml`

## Uso

### Uso Básico

1. Defina sua classe de dados com atributos de sanitização:

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

class UserProfile
{
    #[Sanitize(processors: ['trim', 'html_special_chars'])]
    private string $name = '';

    #[Sanitize(processors: ['trim', 'email_sanitizer'])]
    private string $email = '';

    // Getters e setters...
}
```

2. Configure o sanitizador e utilize-o:

```php
use KaririCode\ProcessorPipeline\ProcessorRegistry;
use KaririCode\Sanitizer\Sanitizer;
use KaririCode\Sanitizer\Processor\Input\TrimSanitizer;
use KaririCode\Sanitizer\Processor\Input\HtmlSpecialCharsSanitizer;
use KaririCode\Sanitizer\Processor\Input\EmailSanitizer;

$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'email_sanitizer', new EmailSanitizer());

$sanitizer = new Sanitizer($registry);

$userProfile = new UserProfile();
$userProfile->setName("  Walmir Silva <script>alert('xss')</script>  ");
$userProfile->setEmail(" walmir.silva@gmail.con ");

$result = $sanitizer->sanitize($userProfile);

echo $userProfile->getName(); // Output: "Walmir Silva"
echo $userProfile->getEmail(); // Output: "walmir.silva@gmail.com"
```

### Uso Avançado: Sanitização de Postagem de Blog

Aqui está um exemplo de como usar o KaririCode Sanitizer em um cenário do mundo real, como a sanitização do conteúdo de uma postagem de blog:

```php
use KaririCode\Sanitizer\Attribute\Sanitize;

class BlogPost
{
    #[Sanitize(
        processors: ['trim', 'html_special_chars', 'xss_sanitizer'],
        messages: [
            'trim' => 'O título foi ajustado',
            'html_special_chars' => 'Caracteres especiais no título foram escapados',
            'xss_sanitizer' => 'Tentativa de XSS removida do título',
        ]
    )]
    private string $title = '';

    #[Sanitize(
        processors: ['trim', 'markdown', 'html_purifier'],
        messages: [
            'trim' => 'O conteúdo foi ajustado',
            'markdown' => 'Markdown no conteúdo foi processado',
            'html_purifier' => 'HTML no conteúdo foi purificado',
        ]
    )]
    private string $content = '';

    // Getters e setters...
}

// Exemplo de uso
$blogPost = new BlogPost();
$blogPost->setTitle("  Explorando o KaririCode: Um Framework PHP Moderno <script>alert('xss')</script>  ");
$blogPost->setContent("# Introdução
KaririCode é um framework PHP **poderoso** e _flexível_ projetado para o desenvolvimento web moderno.");

$result = $sanitizer->sanitize($blogPost);

// Acessar dados sanitizados
echo $blogPost->getTitle(); // Título sanitizado
echo $blogPost->getContent(); // Conteúdo sanitizado
```

## Sanitizadores Disponíveis

### Sanitizadores de Entrada

- **TrimSanitizer**: Remove espaços em branco do início e do final de uma string.

  - **Opções de Configuração**:
    - `characterMask`: Especifica quais caracteres aparar. O padrão é espaço em branco.
    - `trimLeft`: Booleano para aparar do lado esquerdo. O padrão é `true`.
    - `trimRight`: Booleano para aparar do lado direito. O padrão é `true`.

- **HtmlSpecialCharsSanitizer**: Converte caracteres especiais em entidades HTML para evitar ataques XSS.

  - **Opções de Configuração**:
    - `flags`: Flags configuráveis como `ENT_QUOTES | ENT_HTML5`.
    - `encoding`: Codificação de caracteres, por exemplo, 'UTF-8'.
    - `doubleEncode`: Booleano para evitar dupla codificação. O padrão é `true`.

- **NormalizeLineBreaksSanitizer**: Padroniza quebras de linha em diferentes sistemas operacionais.

  - **Opções de Configuração**:
    - `lineEnding`: Especifica o estilo de quebra de linha. Opções: 'unix', 'windows', 'mac'.

- **EmailSanitizer**: Valida e corrige erros comuns de digitação em e-mails, normaliza o formato do e-mail e lida com espaços em branco.

  - **Opções de Configuração**:
    - `removeMailtoPrefix`: Booleano para remover o prefixo 'mailto:'. O padrão é `false`.
    - `typoReplacements`: Array associativo de correções de erros de digitação comuns.
    - `domainReplacements`: Corrige nomes de domínio com erros de digitação comuns.

- **PhoneSanitizer**: Formata e valida números de telefone, incluindo suporte internacional e opções de formatação personalizada.

  - **Opções de Configuração**:
    - `applyFormat`: Booleano para aplicar formatação. O padrão é `false`.
    - `format`: Padrão de formatação personalizado para números de telefone.
    - `placeholder`: Caractere usado como placeholder na formatação.

- **AlphanumericSanitizer**: Remove caracteres não alfanuméricos, com opções configuráveis para permitir certos caracteres especiais.

  - **Opções de Configuração**:
    - `allowSpace`, `allowUnderscore`, `allowDash`, `allowDot`: Opções booleanas para permitir caracteres específicos.
    - `preserveCase`: Booleano para manter a sensibilidade a maiúsculas e minúsculas.

- **UrlSanitizer**: Valida e normaliza URLs, garantindo o protocolo e a estrutura adequados.

  - **Opções de Configuração**:
    - `enforceProtocol`: Impõe um protocolo específico, por exemplo, 'https://'.
    - `defaultProtocol`: O protocolo a ser aplicado se nenhum estiver presente.
    - `removeTrailingSlash`: Booleano para remover a barra final.

- **NumericSanitizer**: Garante que a entrada seja um valor numérico, com opções para números decimais e negativos.

  - **Opções de Configuração**:
    - `allowDecimal`, `allowNegative`: Opções booleanas para permitir decimais e valores negativos.
    - `decimalSeparator`: Especifica o caractere usado para decimais.

- **StripTagsSanitizer**: Remove tags HTML e PHP da entrada, com opções configuráveis para tags permitidas.
  - **Opções de Configuração**:
    - `allowedTags`: Lista de tags HTML a serem mantidas.
    - `keepSafeAttributes`: Booleano para manter certos atributos seguros.
    - `safeAttributes`: Array de atributos a serem preservados.

### Sanitizadores de Domínio

- **HtmlPurifierSanitizer**: Sanitiza conteúdo HTML removendo tags e atributos inseguros, garantindo uma renderização segura do HTML.

  - **Opções de Configuração**:
    - `allowedTags`: Especifica quais tags são permitidas.
    - `allowedAttributes`: Define atributos permitidos para cada tag.
    - `removeEmptyTags`, `removeComments`: Booleano para remover tags vazias ou comentários HTML.
    - `htmlEntities`: Converte caracteres em entidades HTML. O padrão é `true`.

- **JsonSanitizer**: Valida e formata strings JSON, remove caracteres inválidos e garante a estrutura correta do JSON.

  - **Opções de Configuração**:
    - `prettyPrint`: Booleano para formatar o JSON de forma legível.
    - `removeInvalidCharacters`: Booleano para remover caracteres inválidos do JSON.
    - `validateUnicode`: Booleano para validar caracteres Unicode.

- **MarkdownSanitizer**: Processa e sanitiza conteúdo Markdown, escapando caracteres especiais e preservando a estrutura do Markdown.
  - **Opções de Configuração**:
    - `allowedElements`: Especifica elementos Markdown permitidos (por exemplo, 'p', 'h1', 'a').
    - `escapeSpecialCharacters`: Booleano para escapar caracteres especiais como '\*', '\_', etc.
    - `preserveStructure`: Booleano para manter a formatação Markdown.

### Sanitizadores de Segurança

- **FilenameSanitizer**: Garante que nomes de arquivos sejam seguros para uso em sistemas de arquivos, removendo caracteres inseguros e validando extensões.

  - **Opções de Configuração**:
    - `replacement`: Caractere usado para substituir caracteres inseguros. O padrão é `'-'`.
    - `preserveExtension`: Booleano para manter a extensão do arquivo.
    - `blockDangerousExtensions`: Booleano para bloquear extensões como '.exe', '.js'.
    - `allowedExtensions`: Array de extensões permitidas.

- **SqlInjectionSanitizer**: Protege contra ataques de injeção de SQL escapando caracteres especiais e removendo conteúdo potencialmente prejudicial.

  - **Opções de Configuração**:
    - `escapeMap`: Array de caracteres a serem escapados.
    - `removeComments`: Booleano para remover comentários SQL.
    - `escapeQuotes`: Booleano para escapar aspas em consultas SQL.

- **XssSanitizer**: Previne ataques de Cross-Site Scripting (XSS) removendo scripts maliciosos, atributos e garantindo uma saída HTML segura.
  - **Opções de Configuração**:
    - `removeScripts`: Booleano para remover tags `<script>`.
    - `removeEventHandlers`: Booleano para remover manipuladores de eventos 'on\*'.
    - `encodeHtmlEntities`: Booleano para codificar caracteres inseguros.

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

## Integração com Outros Componentes do KaririCode

O componente Sanitizer foi projetado para funcionar perfeitamente com outros componentes do KaririCode:

- **KaririCode\Contract**: Fornece interfaces e contratos para integração consistente de componentes.
- **KaririCode\ProcessorPipeline**: Utilizado para construir e executar pipelines de sanitização.
- **KaririCode\PropertyInspector**: Usado para analisar e processar propriedades de objetos com atributos de sanitização.

## Explicação do Registro

O registro é uma parte central de como os sanitizadores são gerenciados dentro do KaririCode Framework. Ele atua como um local centralizado para registrar e configurar todos os sanitizadores que você planeja usar em sua aplicação.

Veja como você pode criar e configurar o registro:

```php
// Criar e configurar o registro
$registry = new ProcessorRegistry();

// Registrar todos os processadores necessários
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());
$registry->register('sanitizer', 'html_purifier', new HtmlPurifierSanitizer());
$registry->register('sanitizer', 'markdown', new MarkdownSanitizer());
$registry->register('sanitizer', 'numeric_sanitizer', new NumericSanitizer());
$registry->register('sanitizer', 'email_sanitizer', new EmailSanitizer());
$registry->register('sanitizer', 'phone_sanitizer', new PhoneSanitizer());
$registry->register('sanitizer', 'url_sanitizer', new UrlSanitizer());
$registry->register('sanitizer', 'alphanumeric_sanitizer', new AlphanumericSanitizer());
$registry->register('sanitizer', 'filename_sanitizer', new FilenameSanitizer());
$registry->register('sanitizer', 'json_sanitizer', new JsonSanitizer());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());
$registry->register('sanitizer', 'sql_injection', new SqlInjectionSanitizer());
$registry->register('sanitizer', 'strip_tags', new StripTagsSanitizer());
```

Este código demonstra como registrar vários sanitizadores no registro, permitindo que você gerencie facilmente quais sanitizadores estão disponíveis em toda a sua aplicação. Cada sanitizador recebe um identificador único, que pode ser referenciado em atributos para aplicar regras específicas de sanitização.

## Desenvolvimento e Testes

Para fins de desenvolvimento e testes, este pacote usa Docker e Docker Compose para garantir a consistência entre diferentes ambientes. Um Makefile é fornecido para conveniência.

### Pré-requisitos

- Docker
- Docker Compose
- Make (opcional, mas recomendado para facilitar a execução de comandos)

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

3. Inicie os contêineres Docker:

   ```bash
   make up
   ```

4. Instale as dependências:

   ```bash
   make composer-install
   ```

### Comandos Disponíveis do Make

- `make up`: Inicia todos os serviços em segundo plano
- `make down`: Para e remove todos os contêineres
- `make build`: Constrói as imagens do Docker
- `make shell`: Acessa o shell do contêiner PHP
- `make test`: Executa os testes
- `make coverage`: Executa a cobertura de testes com formatação visual
- `make cs-fix`: Executa o PHP CS Fixer para corrigir o estilo do código
- `make quality`: Executa todos os comandos de qualidade (cs-check, test, security-check)

Para uma lista completa dos comandos disponíveis, execute:

```bash
make help
```

## Contribuindo

Contribuições ao componente KaririCode Sanitizer são bem-vindas! Veja como você pode contribuir:

1. Faça um fork do repositório
2. Crie uma nova branch para sua feature ou correção de bug
3. Escreva testes para suas alterações
4. Implemente suas alterações
5. Execute a suíte de testes e garanta que todos os testes passem
6. Envie um pull request com uma descrição clara das suas alterações

Leia nosso [Guia de Contribuição](CONTRIBUTING.md) para mais detalhes sobre nosso código de conduta e processo de desenvolvimento.

## Licença

Este projeto está licenciado sob a licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte e Comunidade

- **Documentação**: [https://kariricode.org/docs/sanitizer](https://kariricode.org/docs/sanitizer)
- **Rastreador de Problemas**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-sanitizer/issues)
- **Fórum da Comunidade**: [KaririCode Club Community](https://kariricode.club)
- **Stack Overflow**: Marque suas perguntas com `kariricode-sanitizer`

---

Construído com ❤️ pela equipe KaririCode. Empoderando desenvolvedores para criar aplicações PHP mais seguras e robustas.
