<?php

namespace Docker\Tests;

use Docker\Context;

use Symfony\Component\Process\Process;

class ContextTest extends TestCase
{
    public function testRemovesFilesOnDestruct()
    {
        $context = new Context();
        $dir = $context->write();

        $this->assertFileExists($dir.'/Dockerfile');

        unset($context);

        $this->assertFileNotExists($dir.'/Dockerfile');
    }

    public function testWritesContextToDisk()
    {
        $context = new Context();
        $dir = $context->write();

        $this->assertFileExists($dir.'/Dockerfile');
    }

    public function testHasDefaultFrom()
    {
        $context = new Context();
        $dir = $context->write();

        $this->assertStringEqualsFile($dir.'/Dockerfile', 'FROM base');
    }

    public function testUsesCustomFrom()
    {
        $context = new Context();
        $context->from('ubuntu:precise');

        $dir = $context->write();

        $this->assertStringEqualsFile($dir.'/Dockerfile', 'FROM ubuntu:precise');
    }

    public function testCreatesTmpDirectory()
    {
        $context = new Context();
        $dir = $context->getDirectory();

        $this->assertFileExists($dir);
    }

    public function testWriteTmpFiles()
    {
        $context = new Context();
        $file = $context->getFile('random content');

        $this->assertStringEqualsFile($context->getDirectory().'/'.$file, 'random content');
    }

    public function testWritesAddCommands()
    {
        $context = new Context();
        $context->add('/foo', 'foo file content');
        $context->write();

        $expectedFile = $context->getFile('foo file content');
        $basename = basename($expectedFile);

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', <<<DOCKERFILE
FROM base
ADD $basename /foo
DOCKERFILE
);
    }

    public function testWritesRunCommands()
    {
        $context = new Context();
        $context->run('foo command');

        $dir = $context->write();

        $this->assertStringEqualsFile($dir.'/Dockerfile', <<<DOCKERFILE
FROM base
RUN foo command
DOCKERFILE
);
    }

    public function testReturnsValidTarContent()
    {
        $this->markTestSkipped('Something weird when comparing tar contents');

        if (!file_exists('/bin/tar')) {
            $this->markTestSkipped('No /bin/tar on host');
        }

        $context = new Context();
        $context->run('foo command');
        $context->add('/bar', 'bar file content');

        $process = new Process('/bin/tar c .', $context->getDirectory());
        $process->run();

        $this->assertEquals($process->getOutput(), $context->toTar());
    }

    public function testReturnsValidTarStream()
    {
        $context = new Context();
        $context->run('foo command');
        $context->add('/bar', 'bar file content');

        $this->assertInternalType('resource', $context->toStream());
    }
}