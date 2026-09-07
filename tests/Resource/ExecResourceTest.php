<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdExecPostBody;
use Docker\API\Model\ExecIdJsonGetResponse200;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\Stream\DockerRawStream;
use Docker\Tests\TestCase;
use Http\Client\Common\Plugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ExecResourceTest extends TestCase
{
    /**
     * Return the container manager.
     */
    private function getManager()
    {
        return self::getDocker();
    }

    public static function streamProvider(): iterable
    {
        foreach ([false, true] as $upgrade) {
            foreach ([false, true] as $tty) {
                foreach ([false, true] as $empty) {
                    yield sprintf('upgrade=%d tty=%d empty=%d', $upgrade, $tty, $empty) => [$upgrade, $tty, $empty];
                }
            }
        }
    }

    /** @dataProvider streamProvider */
    public function testStartStream(bool $upgrade, bool $tty, bool $empty): void
    {
        $createContainerResult = $this->createContainer();
        try {
            $execConfig = new ContainersIdExecPostBody();
            $execConfig->setAttachStdout(true);
            $execConfig->setAttachStderr(true);
            $execConfig->setTty($tty);
            $execConfig->setCmd($empty ? ['true'] : ['sh', '-c', 'printf stdout; printf stderr >&2']);

            $execCreateResult = $this->getManager()->containerExec($createContainerResult->getId(), $execConfig);

            $execStartConfig = new ExecIdStartPostBody();
            $execStartConfig->setDetach(false);
            $execStartConfig->setTty($tty);
            $capture = new class($upgrade) implements Plugin {
                public ?int $status = null;

                public function __construct(private bool $upgrade)
                {
                }

                public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
                {
                    if (!preg_match('#/exec/[^/]+/start$#', $request->getUri()->getPath())) {
                        return $next($request);
                    }
                    if ($this->upgrade) {
                        $request = $request->withHeader('Connection', 'Upgrade')->withHeader('Upgrade', 'tcp');
                    }

                    return $next($request)->then(function (ResponseInterface $response): ResponseInterface {
                        $this->status = $response->getStatusCode();

                        return $response;
                    });
                }
            };
            $docker = Docker::create(null, [$capture]);

            $stream = $docker->execStart($execCreateResult->getId(), $execStartConfig);

            $this->assertSame($upgrade ? 101 : 200, $capture->status);
            $this->assertInstanceOf(DockerRawStream::class, $stream);

            $stdoutFull = '';
            $stderrFull = '';
            $stream->onStdout(function ($stdout) use (&$stdoutFull): void {
                $stdoutFull .= $stdout;
            });
            $stream->onStderr(function ($stderr) use (&$stderrFull): void {
                $stderrFull .= $stderr;
            });
            $stream->wait();

            $this->assertSame($empty ? '' : ($tty ? 'stdoutstderr' : 'stdout'), $stdoutFull);
            $this->assertSame($empty || $tty ? '' : 'stderr', $stderrFull);

            $execInfo = $this->getManager()->execInspect($execCreateResult->getId());
            $this->assertFalse($execInfo->getRunning());
            $this->assertSame(0, $execInfo->getExitCode());
        } finally {
            self::getDocker()->containerDelete($createContainerResult->getId(), ['force' => true]);
        }
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
