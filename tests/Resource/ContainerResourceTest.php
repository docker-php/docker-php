<?php

namespace Docker\Tests\Resource;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\Docker;
use Docker\Tests\TestCase;

class ContainerResourceTest extends TestCase
{
    /**
     * Return the container manager
     */
    private function getManager()
    {
        return self::getDocker();
    }

    /**
     * Be sure to have image before doing test
     */
    public static function setUpBeforeClass()
    {
        self::getDocker()->imageCreate(null, [
            'fromImage' => 'busybox:latest'
        ]);
    }

    public function testAttach()
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['echo', '-n', 'output']);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

        $containerCreateResult = $this->getManager()->containerCreate($containerConfig);
        $dockerRawStream = $this->getManager()->containerAttach($containerCreateResult->getId(), [
            'stream' => true,
            'stdout' => true,
        ]);

        $stdoutFull = "";
        $dockerRawStream->onStdout(function ($stdout) use (&$stdoutFull) {
            $stdoutFull .= $stdout;
        });

        $this->getManager()->containerStart($containerCreateResult->getId());
        $this->getManager()->containerWait($containerCreateResult->getId());

        $dockerRawStream->wait();

        $this->assertEquals("output", $stdoutFull);
    }

    public function testAttachWebsocket()
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sh']);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setAttachStderr(true);
        $containerConfig->setAttachStdin(false);
        $containerConfig->setOpenStdin(true);
        $containerConfig->setTty(true);
        $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

        $containerCreateResult = $this->getManager()->containerCreate($containerConfig);
        $webSocketStream = $this->getManager()->containerAttachWebsocket($containerCreateResult->getId(), [
            'stream' => true,
            'stdout' => true,
            'stderr' => true,
            'stdin'  => true,
        ]);

        $this->getManager()->containerStart($containerCreateResult->getId());

        // Read the bash first line
        $webSocketStream->read();

        // No output after that so it should be false
        $this->assertFalse($webSocketStream->read());

        // Write something to the container
        $webSocketStream->write("echo test\n");

        // Test for echo present (stdin)
        $output = "";

        while (($data = $webSocketStream->read()) != false) {
            $output .= $data;
        }

        $this->assertContains("echo", $output);

        // Exit the container
        $webSocketStream->write("exit\n");
    }

    public function testLogs()
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['echo', '-n', 'output']);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

        $containerCreateResult = $this->getManager()->containerCreate($containerConfig);

        $this->getManager()->containerStart($containerCreateResult->getId());
        $this->getManager()->containerWait($containerCreateResult->getId());

        $logs = $this->getManager()->containerLogs($containerCreateResult->getId(), [
            'stdout' => true,
            'stderr' => true,
        ], Docker::FETCH_OBJECT);


        $this->assertContains("output", $logs['stdout'][0]);
    }
}
