<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdJsonGetResponse200;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Stream\DockerRawStream;
use Docker\Tests\TestCase;

class ExecResourceTest extends TestCase
{
    /**
     * Return the container manager.
     */
    private function getManager()
    {
        return self::getDocker();
    }

    public function testStartStream(): void
    {
        $createContainerResult = $this->createContainer();

        $execConfig = new ContainersIdExecPostBody();
        $execConfig->setAttachStdout(true);
        $execConfig->setAttachStderr(true);
        $execConfig->setCmd(['echo', 'output']);

        $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

        $execStartConfig = new ExecIdStartPostBody();
        $execStartConfig->setDetach(false);
        $execStartConfig->setTty(false);

        $stream = $this->getManager()->execStart($execCreateResult->getId(), $execStartConfig);

        $this->assertInstanceOf(DockerRawStream::class, $stream);

        $stdoutFull = '';
        $stream->onStdout(function ($stdout) use (&$stdoutFull): void {
            $stdoutFull .= $stdout;
        });
        $stream->wait();

        $this->assertSame("output\n", $stdoutFull);

        self::getDocker()->containerKill($createContainerResult->getId(), [
            'signal' => 'SIGKILL',
        ]);
    }

    public function testExecFind(): void
    {
        $createContainerResult = $this->createContainer();

        $execConfig = new ContainersIdExecPostBody();
        $execConfig->setCmd(['/bin/true']);
        $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

        $execStartConfig = new ExecIdStartPostBody();
        $execStartConfig->setDetach(false);
        $execStartConfig->setTty(false);

        $this->getManager()->execStart($execCreateResult->getId(), $execStartConfig);

        $execFindResult = $this->getManager()->execInspect($execCreateResult->getId());

        $this->assertInstanceOf(ExecIdJsonGetResponse200::class, $execFindResult);

        self::getDocker()->containerKill($createContainerResult->getId(), [
            'signal' => 'SIGKILL',
        ]);
    }

    private function createContainer()
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sh']);
        $containerConfig->setOpenStdin(true);
        $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

        $containerCreateResult = self::getDocker()->containerCreate($containerConfig);
        self::getDocker()->containerStart($containerCreateResult->getId());

        return $containerCreateResult;
    }
}
