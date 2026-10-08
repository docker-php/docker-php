<?php

declare(strict_types=1);

namespace Docker\Tests\Context;

use Docker\Context\Dockerignore;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DockerignoreTest extends TestCase
{
    public static function patternProvider(): iterable
    {
        // Examples from https://docs.docker.com/build/concepts/context/#dockerignore-files
        yield 'one level wildcard' => ['*/temp*', ['a/temporary.txt' => true, 'a/temp' => true, 'a/temp/file' => true, 'temp.txt' => false, 'a/b/temp' => false]];
        yield 'two level wildcard' => ['*/*/temp*', ['a/b/temp1' => true, 'a/temp1' => false]];
        yield 'single character' => ['temp?', ['temp1' => true, 'temp12' => false, 'a/temp1' => false]];
        yield 'any depth' => ['**/*.go', ['main.go' => true, 'a/b/c.go' => true, 'a/b/c.gox' => false]];
        yield 'exception' => ["*.md\n!README*.md", ['CHANGES.md' => true, 'README.md' => false, 'README-x.md' => false]];
        yield 'last match wins' => ["*.md\n!README*.md\nREADME-secret.md", ['README.md' => false, 'README-secret.md' => true]];
        yield 'directory and contents' => ['node_modules', ['node_modules' => true, 'node_modules/a/b.js' => true, 'src/node_modules' => false]];
        yield 'leading slash and cleaning' => ["/build/\n./dist/../out", ['build' => true, 'build/x' => true, 'out/y' => true, 'dist' => false]];
        yield 'exception inside excluded directory' => ["docs\n!docs/README.md", ['docs' => true, 'docs/guide.md' => true, 'docs/README.md' => false]];
        yield 'trailing double star keeps the directory' => ['logs/**', ['logs' => false, 'logs/a' => true, 'logs/a/b' => true]];
        yield 'character class' => ["[ab].txt\n[^c]?.log", ['a.txt' => true, 'c.txt' => false, 'd1.log' => true, 'c1.log' => false]];
        yield 'comments and whitespace' => ["# secret\n  secret  \n\n", ['secret' => true, '# secret' => false]];
        yield 'escaped characters' => ["\\#hash\nfile\\*", ['#hash' => true, 'file*' => true, 'filex' => false]];
        yield 'regex characters are literal' => ['a.b+(c)', ['a.b+(c)' => true, 'axb+(c)' => false]];
        yield 'build files are always kept' => ['*', ['Dockerfile' => false, '.dockerignore' => false, 'src' => true]];
    }

    #[DataProvider('patternProvider')]
    public function testMatchesLikeDocker(string $content, array $expected): void
    {
        $dockerignore = new Dockerignore($content);

        foreach ($expected as $path => $excluded) {
            $this->assertSame($excluded, $dockerignore->excludes((string) $path), $path);
        }
    }

    public function testListsIncludedPaths(): void
    {
        $directory = sys_get_temp_dir().'/docker-ignore-'.bin2hex(random_bytes(4));
        foreach (['Dockerfile', 'app.php', 'docs/README.md', 'docs/guide.md', 'node_modules/x/y.js', 'empty/.keep'] as $file) {
            @mkdir(\dirname($directory.'/'.$file), 0777, true);
            touch($directory.'/'.$file);
        }
        file_put_contents($directory.'/.dockerignore', "node_modules\ndocs\n!docs/README.md\nempty/.keep\n");

        try {
            $paths = Dockerignore::fromFile($directory.'/.dockerignore')->paths($directory);
        } finally {
            exec('rm -rf '.escapeshellarg($directory));
        }

        $this->assertSame(['.dockerignore', 'Dockerfile', 'app.php', 'docs/README.md', 'empty'], $paths);
    }

    public function testRejectsInvalidPattern(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Dockerignore('[abc');
    }
}
