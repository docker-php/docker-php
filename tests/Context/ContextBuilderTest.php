<?php

declare(strict_types=1);

namespace Docker\Tests\Context;

use Docker\Context\ContextBuilder;
use Docker\Tests\TestCase;

class ContextBuilderTest extends TestCase
{
    public function testRemovesFilesOnDestruct(): void
    {
        $contextBuilder = new ContextBuilder();
        $context = $contextBuilder->getContext();

        $this->assertFileExists($context->getDirectory().'/Dockerfile');

        unset($contextBuilder);

        $this->assertFileNotExists($context->getDirectory().'/Dockerfile');
    }

    public function testWritesContextToDisk(): void
    {
        $contextBuilder = new ContextBuilder();
        $context = $contextBuilder->getContext();

        $this->assertFileExists($context->getDirectory().'/Dockerfile');
    }

    public function testHasDefaultFrom(): void
    {
        $contextBuilder = new ContextBuilder();
        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', 'FROM base');
    }

    public function testUsesCustomFrom(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('ubuntu:precise');

        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', 'FROM ubuntu:precise');
    }

    public function testMultipleFrom(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('ubuntu:precise');

        $contextBuilder->from('test');

        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertSame("FROM ubuntu:precise\nFROM test", $content);
    }

    public function testCreatesTmpDirectory(): void
    {
        $contextBuilder = new ContextBuilder();
        $context = $contextBuilder->getContext();

        $this->assertFileExists($context->getDirectory());
    }

    public function testWriteTmpFiles(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->add('/foo', 'random content');

        $context = $contextBuilder->getContext();
        $filename = \preg_replace(<<<DOCKERFILE
#FROM base
ADD (.+?) /foo#
DOCKERFILE
            , '$1', $context->getDockerfileContent());

        $this->assertStringEqualsFile($context->getDirectory().'/'.$filename, 'random content');
    }

    public function testWriteTmpFileFromStream(): void
    {
        $contextBuilder = new ContextBuilder();
        $stream = \fopen('php://temp', 'r+');
        $this->assertSame(7, \fwrite($stream, 'test123'));
        \rewind($stream);
        $contextBuilder->addStream('/foo', $stream);

        $context = $contextBuilder->getContext();
        $filename = \preg_replace(<<<DOCKERFILE
#FROM base
ADD (.+?) /foo#
DOCKERFILE
            , '$1', $context->getDockerfileContent());
        $this->assertStringEqualsFile($context->getDirectory().'/'.$filename, 'test123');
    }

    public function testWriteTmpFileFromDisk(): void
    {
        $contextBuilder = new ContextBuilder();
        $file = \tempnam('', '');
        \file_put_contents($file, 'abc');
        $this->assertStringEqualsFile($file, 'abc');
        $contextBuilder->addFile('/foo', $file);

        $context = $contextBuilder->getContext();
        $filename = \preg_replace(<<<DOCKERFILE
#FROM base
ADD (.+?) /foo#
DOCKERFILE
            , '$1', $context->getDockerfileContent());
        $this->assertStringEqualsFile($context->getDirectory().'/'.$filename, 'abc');
    }

    public function testWritesAddCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->add('/foo', 'foo file content');

        $context = $contextBuilder->getContext();

        $this->assertRegExp(<<<DOCKERFILE
#FROM base
ADD .+? /foo#
DOCKERFILE
            , $context->getDockerfileContent()
        );
    }

    public function testWritesRunCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->run('foo command');

        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', <<<DOCKERFILE
FROM base
RUN foo command
DOCKERFILE
        );
    }

    public function testWritesEnvCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->env('foo', 'bar');

        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', <<<DOCKERFILE
FROM base
ENV foo bar
DOCKERFILE
        );
    }

    public function testWritesCopyCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->copy('/foo', '/bar');

        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', <<<DOCKERFILE
FROM base
COPY /foo /bar
DOCKERFILE
        );
    }

    public function testWritesWorkdirCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->workdir('/foo');

        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', <<<DOCKERFILE
FROM base
WORKDIR /foo
DOCKERFILE
        );
    }

    public function testWritesExposeCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->expose('80');

        $context = $contextBuilder->getContext();

        $this->assertStringEqualsFile($context->getDirectory().'/Dockerfile', <<<DOCKERFILE
FROM base
EXPOSE 80
DOCKERFILE
        );
    }
}
