<?php

declare(strict_types=1);

namespace KaririCode\Sanitizer\Tests\Unit\Attribute;

use KaririCode\Sanitizer\Attribute\Sanitize;
use KaririCode\Sanitizer\Core\AttributeSanitizer;
use KaririCode\Sanitizer\Provider\SanitizerServiceProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(AttributeSanitizer::class)]
#[CoversClass(Sanitize::class)]
final class AttributeSanitizerTest extends TestCase
{
    #[Test]
    public function testSanitizeDtoViaAttributes(): void
    {
        $dto = new class () {
            #[Sanitize('trim', 'lower_case')]
            public string $email = '  User@Test.COM  ';

            #[Sanitize('trim')]
            #[Sanitize('capitalize')]
            public string $name = '  walmir silva  ';

            public string $untouched = 'no rules';
        };

        $sanitizer = new SanitizerServiceProvider()->createAttributeSanitizer();
        $result = $sanitizer->sanitize($dto);

        $this->assertSame('user@test.com', $dto->email);
        $this->assertSame('Walmir Silva', $dto->name);
        $this->assertSame('no rules', $dto->untouched);
        $this->assertTrue($result->wasModified());
    }

    #[Test]
    public function testParameterizedAttributeRules(): void
    {
        $dto = new class () {
            #[Sanitize(['truncate', ['max' => 10, 'suffix' => '…']])]
            public string $bio = 'This is a very long text';
        };

        $sanitizer = new SanitizerServiceProvider()->createAttributeSanitizer();
        $sanitizer->sanitize($dto);

        $this->assertSame('This is a…', $dto->bio);
    }
}
