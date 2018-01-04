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
        $directory = __DIR__.DIRECTORY_SEPARATOR.'context-test';

        $context = new Context($directory);
        $process = new Process('/usr/bin/env tar c .', $directory);
        $process->run();

        $this->assertSame(\strlen($process->getOutput()), \strlen($context->toTar()));
    }

    public function testReturnsValidTarStream(): void
    {
        $directory = __DIR__.DIRECTORY_SEPARATOR.'context-test';

        $context = new Context($directory);
        $this->assertInternalType('resource', $context->toStream());
    }

    public function testDirectorySetter(): void
    {
        $context = new Context('abc');
        $this->assertSame('abc', $context->getDirectory());
        $context->setDirectory('def');
        $this->assertSame('def', $context->getDirectory());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testTarFailed(): void
    {
        $directory = __DIR__.DIRECTORY_SEPARATOR.'context-test';
        $path = \getenv('PATH');
        \putenv('PATH=/');
        $context = new Context($directory);
        try {
            $context->toTar();
        } finally {
            \putenv("PATH=$path");
        }
    }

    public function testRemovesFilesOnDestruct(): void
    {
        $context = (new ContextBuilder())->getContext();
        $file = $context->getDirectory().'/Dockerfile';
        $this->assertFileExists($file);

        unset($context);

        $this->assertFileNotExists($file);
    }
}
