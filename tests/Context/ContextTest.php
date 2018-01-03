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

    public function testRemovesFilesOnDestruct(): void
    {
        $context = (new ContextBuilder())->getContext();
        $file = $context->getDirectory().'/Dockerfile';
        $this->assertFileExists($file);

        unset($context);

        $this->assertFileNotExists($file);
    }


}
