<?php

declare(strict_types=1);

namespace Docker\Tests;

use Amp\Loop;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\DockerAsync;

class DockerAsyncTest extends \PHPUnit\Framework\TestCase
{
    public function testStaticConstructor(): void
    {
        $this->assertInstanceOf(DockerAsync::class, DockerAsync::create());
    }

    public function testAsync(): void
    {
        Loop::run(function () {
            $docker = DockerAsync::create();

            $containerConfig = new ContainersCreatePostBody();
            $containerConfig->setImage('busybox:latest');
            $containerConfig->setCmd(['echo', '-n', 'output']);
            $containerConfig->setAttachStdout(true);
            $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

            $response = yield $docker->imageCreate('', [
                'fromImage' => 'busybox:latest',
            ], [], DockerAsync::FETCH_RESPONSE);

            yield $response->getBody();

            $containerCreate = yield $docker->containerCreate($containerConfig);
            $containerStart = yield $docker->containerStart($containerCreate->getId());
            $containerInfo = yield $docker->containerInspect($containerCreate->getId());

            $this->assertSame($containerCreate->getId(), $containerInfo->getId());
        });
    }
}
