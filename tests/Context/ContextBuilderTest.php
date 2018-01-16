<?php

declare(strict_types=1);

namespace Docker\Tests\Context;

use Docker\Context\Context;
use Docker\Context\ContextBuilder;
use Docker\Tests\TestCase;

class ContextBuilderTest extends TestCase
{
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

    public function testWriteTmpDirFromDisk(): void
    {
        $contextBuilder = new ContextBuilder();
        $dir = \tempnam(\sys_get_temp_dir(), '');
        \unlink($dir);
        \mkdir($dir);
        \file_put_contents($dir.'/test', 'abc');
        $this->assertStringEqualsFile($dir.'/test', 'abc');
        $contextBuilder->addFile('/foo', $dir);

        $context = $contextBuilder->getContext();
        $filename = \preg_replace(<<<DOCKERFILE
#FROM base
ADD (.+?) /foo#
DOCKERFILE
            , '$1', $context->getDockerfileContent());
        $this->assertStringEqualsFile($context->getDirectory().'/'.$filename.'/test', 'abc');
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

    public function testWritesUserCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->user('user1');
        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertStringEndsWith("\nUSER user1", $content);

        $contextBuilder->user('user2');
        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertStringEndsWith("\nUSER user1\nUSER user2", $content);
    }

    public function testWritesVolumeCommands(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->volume('volume1');
        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertStringEndsWith("\nVOLUME volume1", $content);

        $contextBuilder->volume('volume2');
        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertStringEndsWith("\nVOLUME volume1\nVOLUME volume2", $content);
    }

    public function testWritesCommandCommand(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->command('test123');

        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertStringEndsWith("\nCMD test123", $content);

        $contextBuilder->command('changed');
        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertNotContains('CMD test123', $content);
        $this->assertStringEndsWith("\nCMD changed", $content);
    }

    public function testWritesEntrypointCommand(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->entrypoint('test123');

        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertStringEndsWith("\nENTRYPOINT test123", $content);

        $contextBuilder->entrypoint('changed');
        $content = $contextBuilder->getContext()->getDockerfileContent();
        $this->assertNotContains('ENTRYPOINT test123', $content);
        $this->assertStringEndsWith("\nENTRYPOINT changed", $content);
    }

    public function testTar(): void
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->setFormat(Context::FORMAT_TAR);
        $context = $contextBuilder->getContext();
        $content = $context->read();
        $this->assertInternalType('string', $content);
        $this->assertSame($context->toTar(), $content);
    }

    public function testTraverseSymlinks(): void
    {
        $contextBuilder = new ContextBuilder();
        $dir = \tempnam('', '');
        \unlink($dir);
        \mkdir($dir);
        $file = $dir.'/test';

        \file_put_contents($file, 'abc');

        $linkFile = $file.'-symlink';
        \symlink($file, $linkFile);

        $contextBuilder->addFile('/foo', $dir);

        $context = $contextBuilder->getContext();

        $filename = \preg_replace(<<<DOCKERFILE
#FROM base
ADD (.+?) /foo#
DOCKERFILE
            , '$1', $context->getDockerfileContent());
        \unlink($file);
        $context->setCleanup(false);
        $this->assertStringEqualsFile($context->getDirectory().'/'.$filename.'/test-symlink', 'abc');
    }
}
