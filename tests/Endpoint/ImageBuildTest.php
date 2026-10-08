<?php

declare(strict_types=1);

namespace Docker\Tests\Endpoint;

use Docker\Endpoint\ImageBuild;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

class ImageBuildTest extends TestCase
{
    public static function tagProvider(): iterable
    {
        yield 'single tag' => ['app:latest', ['t=app%3Alatest']];
        yield 'several tags' => [['app:latest', 'app:1.0'], ['t=app%3Alatest', 't=app%3A1.0']];
        yield 'keyed tags' => [['a' => 'app:latest', 'b' => 'app:1.0'], ['t=app%3Alatest', 't=app%3A1.0']];
        yield 'no tags' => [[], []];
    }

    #[DataProvider('tagProvider')]
    public function testRepeatsTagParameter(string|array $tags, array $expected): void
    {
        $query = (new ImageBuild(null, ['t' => $tags]))->getQueryString();
        $parameters = explode('&', $query);

        $this->assertSame($expected, array_values(array_filter($parameters, static fn (string $pair): bool => str_starts_with($pair, 't='))));
        $this->assertContains('dockerfile=Dockerfile', $parameters);
    }

    public function testRejectsNonStringTags(): void
    {
        $this->expectException(InvalidOptionsException::class);

        (new ImageBuild(null, ['t' => ['app:latest', 1]]))->getQueryString();
    }
}
