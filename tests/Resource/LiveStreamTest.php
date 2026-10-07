<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Stream\DockerRawStream;
use Docker\Tests\TestCase;

class LiveStreamTest extends TestCase
{
    public static function ttyProvider(): iterable
    {
        yield 'multiplexed stdout and stderr' => [false];
        yield 'TTY output' => [true];
    }

    private function createContainer(bool $tty, array $command): string
    {
        $config = new ContainersCreatePostBody();
        $config->setImage('busybox:latest');
        $config->setCmd($command);
        $config->setTty($tty);
        $config->setExposedPorts(['80/tcp' => new \stdClass()]);
        $config->setLabels(['docker-php-test' => 'true']);
        $id = self::getDocker()->containerCreate($config)->getId();
        self::getDocker()->containerStart($id);

        return $id;
    }

    private function assertLiveOutput(DockerRawStream $stream, bool $tty): void
    {
        $stdout = '';
        $stderr = '';
        $first = null;
        $last = null;
        $stream->onStdout(static function (string $chunk) use (&$stdout, &$first, &$last): void {
            $first ??= hrtime(true);
            $last = hrtime(true);
            $stdout .= $chunk;
        });
        $stream->onStderr(static function (string $chunk) use (&$stderr, &$first, &$last): void {
            $first ??= hrtime(true);
            $last = hrtime(true);
            $stderr .= $chunk;
        });
        $stream->wait();
        self::assertSame($tty ? "first\r\nerror\r\nlast\r\nfinal\r\n" : "first\nlast\n", $stdout);
        self::assertSame($tty ? '' : "error\nfinal\n", $stderr);
        self::assertNotNull($first);
        self::assertGreaterThan(500_000_000, $last - $first, 'Callbacks must receive output before the command finishes, not a buffered response.');
    }

    /** @dataProvider ttyProvider */
    public function testFollowLogsDeliversOutputWhileContainerRuns(bool $tty): void
    {
        $id = $this->createContainer($tty, ['sh', '-c', 'printf "first\n"; printf "error\n" >&2; sleep 2; printf "last\n"; printf "final\n" >&2']);
        try {
            $stream = self::getDocker()->containerLogs($id, ['stdout' => true, 'stderr' => true, 'follow' => true]);
            self::assertInstanceOf(DockerRawStream::class, $stream);
            $this->assertLiveOutput($stream, $tty);
            self::assertNull(self::getDocker()->containerInspect($id)->getNetworkSettings()->getPorts()['80/tcp']);
        } finally {
            self::getDocker()->containerDelete($id, ['force' => true]);
        }
    }

    /** @dataProvider ttyProvider */
    public function testExecDeliversOutputBeforeExit(bool $tty): void
    {
        $id = $this->createContainer(false, ['sleep', '30']);
        try {
            $config = new ContainersIdExecPostBody();
            $config->setAttachStdout(true);
            $config->setAttachStderr(true);
            $config->setTty($tty);
            $config->setCmd(['sh', '-c', 'printf "first\n"; printf "error\n" >&2; sleep 2; printf "last\n"; printf "final\n" >&2']);
            $exec = self::getDocker()->containerExec($id, $config);
            $start = new ExecIdStartPostBody();
            $start->setDetach(false);
            $start->setTty($tty);
            $stream = self::getDocker()->execStart($exec->getId(), $start);
            self::assertInstanceOf(DockerRawStream::class, $stream);
            $this->assertLiveOutput($stream, $tty);
            $result = self::getDocker()->execInspect($exec->getId());
            self::assertFalse($result->getRunning());
            self::assertSame(0, $result->getExitCode());
        } finally {
            self::getDocker()->containerDelete($id, ['force' => true]);
        }
    }
}
