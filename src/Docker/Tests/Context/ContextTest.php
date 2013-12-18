<?php

namespace Docker\Tests\Context;

use Docker\Context\Context;
use Docker\Tests\TestCase;
use Symfony\Component\Process\Process;

class ContextTest extends TestCase
{
    public function testReturnsValidTarContent()
    {
        $directory = __DIR__.DIRECTORY_SEPARATOR."context-test";
        $context   = new Context($directory);
        $tarFile   = tempnam(sys_get_temp_dir(), "docker-test-").".tar";

        file_put_contents($tarFile, $context->toTar());
        $phar = new \PharData($tarFile);

        $this->assertCount(1, $phar);
        $this->assertNotNull($phar['Dockerfile']);
    }

    public function testReturnsValidTarStream()
    {
        $directory = __DIR__.DIRECTORY_SEPARATOR."context-test";

        $context = new Context($directory);
        $this->assertInternalType('resource', $context->toStream());
    }
}