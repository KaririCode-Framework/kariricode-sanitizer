# Framework KaririCode: Componente Sanitizer

[![en](https://img.shields.io/badge/lang-en-red.svg)](README.md) [![pt-br](https://img.shields.io/badge/lang-pt--br-green.svg)](README.pt-br.md)

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white) ![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white) ![PHPUnit](https://img.shields.io/badge/PHPUnit-3776AB?style=for-the-badge&logo=php&logoColor=white)

Um componente robusto e flexível de sanitização de dados para PHP, parte do Framework KaririCode. Utiliza processadores configuráveis e funções nativas para garantir a integridade e segurança dos dados em suas aplicações.

## Índice

- [Características](#características)
- [Instalação](#instalação)
- [Uso](#uso)
  - [Uso Básico](#uso-básico)
  - [Uso Avançado](#uso-avançado)
- [Sanitizadores Disponíveis](#sanitizadores-disponíveis)
- [Integração com Outros Componentes KaririCode](#integração-com-outros-componentes-kariricode)
- [Desenvolvimento e Testes](#desenvolvimento-e-testes)
- [Licença](#licença)
- [Suporte e Comunidade](#suporte-e-comunidade)

## Características

- Sanitização flexível baseada em atributos para propriedades de objetos
- Conjunto abrangente de sanitizadores integrados para casos de uso comuns
- Fácil integração com outros componentes KaririCode
- Processadores configuráveis para lógica de sanitização personalizada
- Suporte para valores de fallback em caso de falhas na sanitização
- Arquitetura extensível permitindo sanitizadores personalizados

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
    #[Sanitize(sanitizers: ['trim', 'html_special_chars'])]
    private string $nome = '';

    #[Sanitize(sanitizers: ['trim', 'normalize_line_breaks'])]
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
$perfilUsuario->setNome("  Walmir Silva  ");
$perfilUsuario->setEmail("walmir.silva@exemplo.com\r\n");

$sanitizer->sanitize($perfilUsuario);

echo $perfilUsuario->getNome(); // Saída: "Walmir Silva"
echo $perfilUsuario->getEmail(); // Saída: "walmir.silva@exemplo.com\n"
```

### Uso Avançado

Você pode criar sanitizadores personalizados implementando as interfaces `Processor` ou `ConfigurableProcessor`:

```php
use KaririCode\Contract\Processor\ConfigurableProcessor;
use KaririCode\Sanitizer\Processor\AbstractSanitizerProcessor;

class SanitizadorPersonalizado extends AbstractSanitizerProcessor implements ConfigurableProcessor
{
    private $opcao;

    public function configure(array $options): void
    {
        $this->opcao = $options['opcao_personalizada'] ?? 'padrao';
    }

    public function process(mixed $input): string
    {
        $input = $this->guardAgainstNonString($input);
        // Lógica de sanitização personalizada aqui
        return $input;
    }
}

// Registre e use o sanitizador personalizado
$registry->register('sanitizer', 'personalizado', new SanitizadorPersonalizado());

class PerfilAvancado
{
    #[Sanitize(sanitizers: ['personalizado' => ['opcao_personalizada' => 'valor']])]
    private string $campoPersonalizado = '';
}
```

## Sanitizadores Disponíveis

O componente Sanitizer fornece vários sanitizadores integrados:

### Sanitizadores de Entrada

- TrimSanitizer
- HtmlSpecialCharsSanitizer
- NormalizeLineBreaksSanitizer
- StripTagsSanitizer

### Sanitizadores de Domínio

- HtmlPurifierSanitizer
- JsonSanitizer
- MarkdownSanitizer

### Sanitizadores de Segurança

- FilenameSanitizer
- SqlInjectionSanitizer
- XssSanitizer

Cada sanitizador é projetado para lidar com tipos específicos de dados e preocupações de segurança. Para informações detalhadas sobre cada sanitizador, consulte a [documentação](https://kariricode.org/docs/sanitizer).

## Integração com Outros Componentes KaririCode

O componente Sanitizer foi projetado para trabalhar perfeitamente com outros componentes KaririCode:

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
// Registre os sanitizadores...

$builder = new ProcessorBuilder($registry);
$attributeHandler = new AttributeHandler('sanitizer', $builder);
$propertyInspector = new PropertyInspector(new AttributeAnalyzer(Sanitize::class));

$sanitizer = new Sanitizer($registry);
```

## Desenvolvimento e Testes

Para fins de desenvolvimento e teste, este pacote usa Docker e Docker Compose para garantir consistência em diferentes ambientes. Um Makefile é fornecido para conveniência.

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

3. Inicie os contêineres Docker:

   ```bash
   make up
   ```

4. Instale as dependências:
   ```bash
   make composer-install
   ```

### Comandos Make Disponíveis

- `make up`: Inicia todos os serviços em segundo plano
- `make down`: Para e remove todos os contêineres
- `make build`: Constrói imagens Docker
- `make shell`: Acessa o shell do contêiner PHP
- `make test`: Executa testes
- `make coverage`: Executa cobertura de testes com formatação visual
- `make cs-fix`: Executa PHP CS Fixer para corrigir o estilo do código
- `make quality`: Executa todos os comandos de qualidade (cs-check, test, security-check)

Para uma lista completa de comandos disponíveis, execute:

```bash
make help
```

## Licença

Este projeto está licenciado sob a Licença MIT - veja o arquivo [LICENSE](LICENSE) para detalhes.

## Suporte e Comunidade

- **Documentação**: [https://kariricode.org/docs/sanitizer](https://kariricode.org/docs/sanitizer)
- **Rastreador de Problemas**: [GitHub Issues](https://github.com/KaririCode-Framework/kariricode-sanitizer/issues)
- **Comunidade**: [Comunidade KaririCode Club](https://kariricode.club)

---

Construído com ❤️ pela equipe KaririCode. Capacitando desenvolvedores para criar aplicações PHP mais seguras e robustas.
