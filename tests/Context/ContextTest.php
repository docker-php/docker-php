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
}
