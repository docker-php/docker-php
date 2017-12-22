<?php

declare(strict_types=1);

namespace Docker\Tests\Context;

use Docker\Context\Context;
use Docker\Tests\TestCase;
use Symfony\Component\Process\Exception\ProcessFailedException;
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

    public function testDirectorySetter()
    {
        $context = new Context('abc');
        $this->assertEquals('abc', $context->getDirectory());
        $context->setDirectory('def');
        $this->assertEquals('def', $context->getDirectory());
    }

    /**
     * @expectedException \Symfony\Component\Process\Exception\ProcessFailedException
     */
    public function testTarFailed()
    {
        $directory = __DIR__.DIRECTORY_SEPARATOR."context-test";
        $path = getenv('PATH');
        putenv('PATH=/');
        $context = new Context($directory);
        try {
            $context->toTar();
        } finally {
            putenv("PATH=$path");
        }
    }
}
