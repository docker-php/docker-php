<?php

namespace Docker\Tests\Context;

use Docker\Context\Context;
use Docker\Tests\TestCase;
use Symfony\Component\Process\Process;

class ContextTest extends TestCase
{
    public function testReturnsValidTarContent()
    {
        if (!file_exists('/bin/tar')) {
            $this->markTestSkipped('No /bin/tar on host');
        }

        $directory = __DIR__.DIRECTORY_SEPARATOR."context-test";

        $context = new Context($directory);
        $process = new Process('/bin/tar c .', $directory);
        $process->run();

        $this->assertEquals(md5($process->getOutput()), md5($context->toTar()));
    }

    public function testReturnsValidTarStream()
    {
        if (!file_exists('/bin/tar')) {
            $this->markTestSkipped('No /bin/tar on host');
        }

        $directory = __DIR__.DIRECTORY_SEPARATOR."context-test";

        $context = new Context($directory);
        $this->assertInternalType('resource', $context->toStream());
    }
}