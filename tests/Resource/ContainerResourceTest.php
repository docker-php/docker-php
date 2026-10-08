<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\Docker;
use Docker\Stream\AttachWebsocketStream;
use Docker\Stream\DockerRawStream;
use Docker\Tests\TestCase;

class ContainerResourceTest extends TestCase
{
    /**
     * Return the container manager.
     */
    private function getManager()
    {
        return self::getDocker();
    }

    /**
     * Be sure to have image before doing test.
     */
    public static function setUpBeforeClass(): void
    {
        // The pull only runs while its progress stream is read.
        self::getDocker()->imageCreate('', [
            'fromImage' => 'busybox:latest',
        ])->wait();
    }

    public function testAttach(): void
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['echo', '-n', 'output']);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setLabels(['docker-php-test' => 'true']);

        $containerCreateResult = $this->getManager()->containerCreate($containerConfig);
        $dockerRawStream = $this->getManager()->containerAttach(
            $containerCreateResult->getId(),
            [
                'stream' => true,
                'stdout' => true,
            ]
        );

        $stdoutFull = '';
        $dockerRawStream->onStdout(function ($stdout) use (&$stdoutFull): void {
            $stdoutFull .= $stdout;
        });

        $this->getManager()->containerStart($containerCreateResult->getId());
        $this->getManager()->containerWait($containerCreateResult->getId());

        $dockerRawStream->wait();

        $this->assertSame('output', $stdoutFull);
    }

    public function testAttachTty(): void
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sh', '-c', 'printf stdout; printf stderr >&2']);
        $containerConfig->setTty(true);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setAttachStderr(true);
        $containerConfig->setLabels(['docker-php-test' => 'true']);
        $id = $this->getManager()->containerCreate($containerConfig)->getId();

        try {
            $stream = $this->getManager()->containerAttach($id, ['stream' => true, 'stdout' => true, 'stderr' => true]);
            $output = '';
            $stream->onStdout(static function (string $chunk) use (&$output): void {
                $output .= $chunk;
            });
            $this->getManager()->containerStart($id);
            $this->getManager()->containerWait($id);
            $stream->wait();

            $this->assertSame('stdoutstderr', $output);
        } finally {
            $this->getManager()->containerDelete($id, ['force' => true]);
        }
    }

    public function testAttachWebsocket(): void
    {
        $operatingSystem = (string) $this->getManager()->systemInfo()->getOperatingSystem();
        if (str_contains($operatingSystem, 'Docker Desktop')) {
            $this->markTestSkipped('Docker Desktop\'s socket proxy does not forward WebSocket attach output.');
        }

        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sh']);
        $containerConfig->setAttachStdin(true);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setAttachStderr(true);
        $containerConfig->setOpenStdin(true);
        $containerConfig->setTty(true);
        $containerConfig->setLabels(['docker-php-test' => 'true']);
        $id = $this->getManager()->containerCreate($containerConfig)->getId();

        try {
            $webSocketStream = $this->getManager()->containerAttachWebsocket($id, [
                'stream' => true,
                'stdout' => true,
                'stderr' => true,
                'stdin' => true,
            ]);
            $this->assertInstanceOf(AttachWebsocketStream::class, $webSocketStream);
            $this->getManager()->containerStart($id);

            $webSocketStream->write("echo docker-php-\$((40 + 2))\n");
            $output = '';
            $deadline = microtime(true) + 10;
            while (!str_contains($output, 'docker-php-42') && microtime(true) < $deadline) {
                $data = $webSocketStream->read(0, 200000);
                $this->assertNotNull($data, 'The WebSocket closed before the command output arrived.');
                $output .= (string) $data;
            }
            $this->assertStringContainsString('docker-php-42', $output);

            $webSocketStream->write("exit\n");
            $deadline = microtime(true) + 10;
            while (null !== $webSocketStream->read(0, 200000) && microtime(true) < $deadline) {
            }
            $this->assertNull($webSocketStream->read(), 'The WebSocket should close when the shell exits.');
        } finally {
            $this->getManager()->containerDelete($id, ['force' => true]);
        }
    }

    public static function logsProvider(): iterable
    {
        yield 'separate stdout and stderr' => [false, 'printf stdout; printf stderr >&2', 'stdout', 'stderr'];
        yield 'TTY combines stdout and stderr' => [true, 'printf stdout; printf stderr >&2', 'stdoutstderr', ''];
        yield 'empty non-TTY logs' => [false, 'true', '', ''];
        yield 'empty TTY logs' => [true, 'true', '', ''];
    }

    /** @dataProvider logsProvider */
    public function testLogs(bool $tty, string $command, string $expectedStdout, string $expectedStderr): void
    {
        $containerConfig = new ContainersCreatePostBody();
        $containerConfig->setImage('busybox:latest');
        $containerConfig->setCmd(['sh', '-c', $command]);
        $containerConfig->setAttachStdout(true);
        $containerConfig->setAttachStderr(true);
        $containerConfig->setTty($tty);
        $containerConfig->setLabels(['docker-php-test' => 'true']);

        $containerCreateResult = $this->getManager()->containerCreate($containerConfig);

        try {
            $this->getManager()->containerStart($containerCreateResult->getId());
            $this->getManager()->containerWait($containerCreateResult->getId());

            $logsStream = $this->getManager()->containerLogs(
                $containerCreateResult->getId(),
                [
                    'stdout' => true,
                    'stderr' => true,
                ],
                Docker::FETCH_OBJECT
            );

            self::assertInstanceOf(DockerRawStream::class, $logsStream);
            $stdout = '';
            $stderr = '';
            $logsStream->onStdout(static function (string $output) use (&$stdout): void {
                $stdout .= $output;
            });
            $logsStream->onStderr(static function (string $output) use (&$stderr): void {
                $stderr .= $output;
            });
            $logsStream->wait();

            self::assertSame($expectedStdout, $stdout);
            self::assertSame($expectedStderr, $stderr);
        } finally {
            $this->getManager()->containerDelete($containerCreateResult->getId(), ['force' => true]);
        }
    }
}
