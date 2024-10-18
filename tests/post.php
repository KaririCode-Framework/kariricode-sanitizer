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

class BlogPost
{
    #[Sanitize(
        processors: ['trim', 'html_special_chars', 'xss_sanitizer'],
        messages: [
            'trim' => 'Title was trimmed',
            'html_special_chars' => 'Special characters in title were escaped',
            'xss_sanitizer' => 'XSS attempt was removed from title',
        ]
    )]
    private string $title = '';

    #[Sanitize(
        processors: ['trim', 'normalize_line_breaks'],
        messages: [
            'trim' => 'Slug was trimmed',
            'normalize_line_breaks' => 'Line breaks in slug were normalized',
        ]
    )]
    private string $slug = '';

    #[Sanitize(
        processors: ['trim', 'markdown', 'html_purifier'],
        messages: [
            'trim' => 'Content was trimmed',
            'markdown' => 'Markdown in content was processed',
            'html_purifier' => 'HTML in content was purified',
        ]
    )]
    private string $content = '';

    #[Sanitize(
        processors: ['trim', 'strip_tags', 'html_special_chars'],
        messages: [
            'trim' => 'Author name was trimmed',
            'strip_tags' => 'HTML tags were removed from author name',
            'html_special_chars' => 'Special characters in author name were escaped',
        ]
    )]
    private string $authorName = '';

    // Getters and setters...

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function getAuthorName(): string
    {
        return $this->authorName;
    }

    public function setAuthorName(string $authorName): void
    {
        $this->authorName = $authorName;
    }
}

// Set up the sanitizer
$registry = new ProcessorRegistry();
$registry->register('sanitizer', 'trim', new TrimSanitizer());
$registry->register('sanitizer', 'html_special_chars', new HtmlSpecialCharsSanitizer());
$registry->register('sanitizer', 'normalize_line_breaks', new NormalizeLineBreaksSanitizer());
$registry->register('sanitizer', 'strip_tags', new StripTagsSanitizer());
$registry->register('sanitizer', 'markdown', new MarkdownSanitizer());
$registry->register('sanitizer', 'xss_sanitizer', new XssSanitizer());

// Configure HTML Purifier with specific settings for blog content
$htmlPurifier = new HtmlPurifierSanitizer();
$htmlPurifier->configure([
    'allowedTags' => ['p', 'br', 'strong', 'em', 'u', 'ol', 'ul', 'li', 'a', 'img', 'h2', 'h3', 'blockquote'],
    'allowedAttributes' => ['href' => ['a'], 'src' => ['img'], 'alt' => ['img']],
]);
$registry->register('sanitizer', 'html_purifier', $htmlPurifier);

$sanitizer = new Sanitizer($registry);

// Simulating form submission with potentially unsafe data
$blogPost = new BlogPost();
$blogPost->setTitle("  Exploring KaririCode: A Modern PHP Framework <script>alert('xss')</script>  ");
$blogPost->setSlug(" exploring-kariricode-a-modern-php-framework \r\n");
$blogPost->setContent("
# Introduction

KaririCode is a **powerful** and _flexible_ PHP framework designed for modern web development.

<script>alert('malicious code');</script>

## Key Features

1. Robust sanitization
2. Efficient routing
3. Powerful ORM

Check out our [official website](https://kariricode.org) for more information!

<img src=\"harmful.jpg\" onerror=\"alert('xss')\" />
");

$blogPost->setAuthorName("<b>John Doe</b> <script>alert('xss')</script>");

// Function to display original and sanitized values
function displayBlogPost(BlogPost $post, Sanitizer $sanitizer)
{
    echo "Original Blog Post:\n";
    echo "Title: \"{$post->getTitle()}\"\n";
    echo "Slug: \"{$post->getSlug()}\"\n";
    echo "Content: \"{$post->getContent()}\"\n";
    echo "Author: \"{$post->getAuthorName()}\"\n\n";

    $result = $sanitizer->sanitize($post);

    echo "Sanitized Blog Post:\n";
    echo "Title: \"{$result['object']->getTitle()}\"\n";
    echo "Slug: \"{$result['object']->getSlug()}\"\n";
    echo "Content: \"{$result['object']->getContent()}\"\n";
    echo "Author: \"{$result['object']->getAuthorName()}\"\n\n";

    if (!empty($result['sanitizedValues'])) {
        echo "Sanitization Details:\n";
        foreach ($result['sanitizedValues'] as $property => $data) {
            echo ucfirst($property) . ":\n";
            foreach ($data['messages'] as $processorName => $message) {
                echo "  - [$processorName] $message\n";
            }
        }
    }

    if (!empty($result['errors'])) {
        echo "\nErrors:\n";
        foreach ($result['errors'] as $property => $errors) {
            echo ucfirst($property) . ":\n";
            foreach ($errors as $error) {
                echo "  - $error\n";
            }
        }
    }
}

// Display and sanitize the blog post
displayBlogPost($blogPost, $sanitizer);
