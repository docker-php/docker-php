<?php

declare(strict_types=1);

namespace Docker\Tests\Context;

use Docker\Context\Context;
use Docker\Context\ContextBuilder;
use Docker\Tests\TestCase;
use Symfony\Component\Process\Process;

class ContextTest extends TestCase
{
    public function testReturnsValidTarContent(): void
    {
        $directory = __DIR__.\DIRECTORY_SEPARATOR.'context-test';

        $context = new Context($directory);
        $process = new Process(['/usr/bin/env', 'tar', 'c', '.'], $directory);
        $process->run();

        $this->assertSame(\strlen($process->getOutput()), \strlen($context->toTar()));
    }

    public function testReturnsValidTarStream(): void
    {
        $directory = __DIR__.\DIRECTORY_SEPARATOR.'context-test';

        $context = new Context($directory);
        $this->assertIsResource($context->toStream());
    }

    public function testDirectorySetter(): void
    {
        $context = new Context('abc');
        $this->assertSame('abc', $context->getDirectory());
        $context->setDirectory('def');
        $this->assertSame('def', $context->getDirectory());
    }

    public function testTarFailed(): void
    {
        $this->expectException(\Symfony\Component\Process\Exception\ProcessFailedException::class);

        $directory = __DIR__.\DIRECTORY_SEPARATOR.'context-test';
        $path = getenv('PATH');
        $serverPath = $_SERVER['PATH'] ?? null;
        $environmentPath = $_ENV['PATH'] ?? null;
        putenv('PATH=/');
        $_SERVER['PATH'] = '/';
        $_ENV['PATH'] = '/';
        $context = new Context($directory);
        try {
            $context->toTar();
        } finally {
            putenv(false === $path ? 'PATH' : "PATH=$path");
            if (null === $serverPath) {
                unset($_SERVER['PATH']);
            } else {
                $_SERVER['PATH'] = $serverPath;
            }
            if (null === $environmentPath) {
                unset($_ENV['PATH']);
            } else {
                $_ENV['PATH'] = $environmentPath;
            }
        }
    }

    public function testRemovesFilesOnDestruct(): void
    {
        $context = (new ContextBuilder())->getContext();
        $file = $context->getDirectory().'/Dockerfile';
        $this->assertFileExists($file);

        unset($context);

        $this->assertFileDoesNotExist($file);
    }

    public function testAppliesDockerignoreByDefault(): void
    {
        $directory = $this->contextWithDockerignore();
        $context = new Context($directory);
        $tar = $context->toTar();
        $stream = stream_get_contents($context->toStream());

        $expected = ['./.dockerignore', './Dockerfile', './app.php', './docs', './docs/README.md'];
        $this->assertSame($expected, $this->tarEntries($tar));
        $this->assertSame($expected, $this->tarEntries($stream));
    }

    public function testDockerignoreCanBeDisabled(): void
    {
        $directory = $this->contextWithDockerignore();
        $deprecations = $this->captureDeprecations(static function () use ($directory, &$tar): void {
            $tar = (new Context($directory))->applyDockerignore(false)->toTar();
        });

        $this->assertContains('./secret.txt', $this->tarEntries($tar));
        $this->assertSame([], $deprecations);
    }

    public function testNoDeprecationWithoutDockerignore(): void
    {
        $deprecations = $this->captureDeprecations(static function (): void {
            (new Context(__DIR__.\DIRECTORY_SEPARATOR.'context-test'))->toTar();
        });

        $this->assertSame([], $deprecations);
    }

    private function contextWithDockerignore(): string
    {
        $directory = sys_get_temp_dir().'/docker-context-'.bin2hex(random_bytes(4));
        foreach (['Dockerfile', 'app.php', 'secret.txt', 'docs/README.md', 'docs/guide.md'] as $file) {
            @mkdir(\dirname($directory.'/'.$file), 0777, true);
            file_put_contents($directory.'/'.$file, $file);
        }
        file_put_contents($directory.'/.dockerignore', "secret.txt\ndocs/*\n!docs/README.md\n");
        $this->directories[] = $directory;

        return $directory;
    }

    private array $directories = [];

    protected function tearDown(): void
    {
        foreach ($this->directories as $directory) {
            (new \Symfony\Component\Filesystem\Filesystem())->remove($directory);
        }
    }

    /** @return list<string> */
    private function tarEntries(string $tar): array
    {
        $process = new Process(['/usr/bin/env', 'tar', '-t']);
        $process->setInput($tar);
        $process->mustRun();
        $entries = array_map(static fn (string $entry): string => rtrim($entry, '/'), explode("\n", trim($process->getOutput())));
        sort($entries);

        return $entries;
    }

    /** @return list<string> */
    private function captureDeprecations(callable $callback): array
    {
        $deprecations = [];
        set_error_handler(static function (int $level, string $message) use (&$deprecations): bool {
            $deprecations[] = $message;

            return true;
        }, \E_USER_DEPRECATED);
        try {
            $callback();
        } finally {
            restore_error_handler();
        }

        return $deprecations;
    }
}
