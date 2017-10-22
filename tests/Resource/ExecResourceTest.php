<?php

namespace Docker\Tests\Resource;

use Docker\Resource\OverrideResource;
use Docker\Tests\TestCase;

class ExecResourceTest extends TestCase
{
    /**
     * Return the container manager
     */
    private function getManager()
    {
        return self::getDocker()->exec();
    }

    public function testStartStream()
    {
        $createContainerResult = $this->createContainer();

        $execConfig = self::createModel('ContainersIdExecPostBody');
        $execConfig->setAttachStdout(true);
        $execConfig->setAttachStderr(true);
        $execConfig->setCmd(["echo", "output"]);

        $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

        $execStartConfig = self::createModel('ExecIdStartPostBody');
        $execStartConfig->setDetach(false);
        $execStartConfig->setTty(false);

        $stream = $this->getManager()->execStart($execCreateResult->getId(), $execStartConfig, [], OverrideResource::FETCH_STREAM);

        $this->assertInstanceOf('Docker\Stream\DockerRawStream', $stream);

        $stdoutFull = "";
        $stream->onStdout(function ($stdout) use (&$stdoutFull) {
            $stdoutFull .= $stdout;
        });
        $stream->wait();

        $this->assertEquals("output\n", $stdoutFull);

        self::getDocker()->container()->containerKill($createContainerResult->getId(), [
            'signal' => 'SIGKILL'
        ]);
    }

    public function testExecFind()
    {
        $createContainerResult = $this->createContainer();

        $execConfig = self::createModel('ContainersIdExecPostBody');
        $execConfig->setCmd(["/bin/true"]);
        $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

        $execStartConfig = self::createModel('ExecIdStartPostBody');
        $execStartConfig->setDetach(false);
        $execStartConfig->setTty(false);

        $this->getManager()->execStart($execCreateResult->getId(), $execStartConfig);

        $execFindResult = $this->getManager()->execInspect($execCreateResult->getId());

        $this->assertInstanceOf('Docker\\API\\'.self::getVersion().'\\Model\\ExecIdJsonGetResponse200', $execFindResult);

        self::getDocker()->container()->containerKill($createContainerResult->getId(), [
            'signal' => 'SIGKILL'
        ]);
    }

    private function createContainer()
    {
        $containerConfig = self::createModel('ContainersCreatePostBody');
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sh']);
        $containerConfig->setOpenStdin(true);
        $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

        $containerCreateResult = self::getDocker()->container()->containerCreate($containerConfig);
        self::getDocker()->container()->containerStart($containerCreateResult->getId());

        return $containerCreateResult;
    }
}
