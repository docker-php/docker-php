<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Exception\InteractiveExecTimeoutException;
use Docker\Tests\TestCase;

class InteractiveExecResourceTest extends TestCase
{
    public function testTtyInputAndCombinedOutput(): void
    {
        $docker = self::getDocker();
        $container = new ContainersCreatePostBody();
        $container->setImage('busybox:latest');
        $container->setCmd(['sleep', '60']);
        $created = $docker->containerCreate($container);
        try {
            $docker->containerStart($created->getId());
            $create = new ContainersIdExecPostBody();
            $create->setAttachStdin(true);
            $create->setAttachStdout(true);
            $create->setAttachStderr(true);
            $create->setTty(true);
            $create->setCmd(['sh', '-c', 'read line; printf "ok:%s\\n" "$line"; printf error >&2']);
            $exec = $docker->containerExec($created->getId(), $create);
            $body = new ExecIdStartPostBody();
            $body->setTty(true);
            $session = $docker->execStartInteractive($exec->getId(), $body, 5000);
            $output = '';
            $session->onStdout(static function (string $chunk) use (&$output): void { $output .= $chunk; });
            $session->onStderr(static function (): void { self::fail('TTY stderr was separated.'); });
            self::assertSame(6, $session->writeStdin("hello\n"));
            // TTY input is a terminal: EOF on the socket can cut off output.
            // This command exits after one line, without needing input EOF.
            $session->wait();
            self::assertStringContainsString('ok:hello', $output);
            self::assertStringContainsString('error', $output);
        } finally {
            $docker->containerDelete($created->getId(), ['force' => true]);
        }
    }

    public function testStdinEofAndNonzeroExitAgainstDocker(): void
    {
        $docker = self::getDocker();
        $container = new ContainersCreatePostBody();
        $container->setImage('busybox:latest');
        $container->setCmd(['sleep', '60']);
        $created = $docker->containerCreate($container);
        try {
            $docker->containerStart($created->getId());
            $create = new ContainersIdExecPostBody();
            $create->setAttachStdin(true);
            $create->setAttachStdout(true);
            $create->setAttachStderr(true);
            $create->setTty(false);
            $create->setCmd(['sh', '-c', 'cat; printf error >&2; exit 7']);
            $exec = $docker->containerExec($created->getId(), $create);
            $body = new ExecIdStartPostBody();
            $body->setTty(false);
            $session = $docker->execStartInteractive($exec->getId(), $body, 5000);
            $stdout = $stderr = '';
            $session->onStdout(static function (string $chunk) use (&$stdout): void { $stdout .= $chunk; });
            $session->onStderr(static function (string $chunk) use (&$stderr): void { $stderr .= $chunk; });
            $input = str_repeat("binary\0\xff\n", 8192);
            $offset = 0;
            while ($offset < \strlen($input)) {
                $offset += $session->writeStdin(substr($input, $offset));
                $session->poll(1);
            }
            $session->closeStdin();
            $session->wait();
            self::assertSame($input, $stdout);
            self::assertSame('error', $stderr);
            $info = $docker->execInspect($exec->getId());
            self::assertFalse($info->getRunning());
            self::assertSame(7, $info->getExitCode());
        } finally {
            $docker->containerDelete($created->getId(), ['force' => true]);
        }
    }

    public function testSilentExecHeartbeatAndContainerTimeout(): void
    {
        $docker = self::getDocker();
        $container = new ContainersCreatePostBody();
        $container->setImage('busybox:latest');
        $container->setCmd(['sleep', '60']);
        $created = $docker->containerCreate($container);
        try {
            $docker->containerStart($created->getId());
            $create = new ContainersIdExecPostBody();
            $create->setAttachStdout(true);
            $create->setAttachStderr(true);
            $create->setCmd(['timeout', '-s', 'KILL', '1', 'sleep', '30']);
            $exec = $docker->containerExec($created->getId(), $create);
            $session = $docker->execStartInteractive($exec->getId(), null, 180);
            $ticks = 0;
            try {
                $session->wait(static function () use (&$ticks): void { ++$ticks; }, 30);
                self::fail('The streaming deadline was ignored.');
            } catch (InteractiveExecTimeoutException $expected) {
                self::assertGreaterThanOrEqual(3, $ticks);
            }
            // Disconnecting output is not cancellation. Keep the container-side
            // timeout, which terminates only this exec, not the whole container.
            self::assertTrue($docker->execInspect($exec->getId())->getRunning());
            $deadline = hrtime(true) + 3000000000;
            do {
                usleep(50000);
                $info = $docker->execInspect($exec->getId());
            } while ($info->getRunning() && hrtime(true) < $deadline);
            self::assertFalse($info->getRunning());
            self::assertNotSame(0, $info->getExitCode());
        } finally {
            $docker->containerDelete($created->getId(), ['force' => true]);
        }
    }
}
