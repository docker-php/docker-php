<?php

declare(strict_types=1);

namespace Docker\Tests;

use Amp\Loop;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\DockerAsync;

class DockerAsyncTest extends \PHPUnit\Framework\TestCase
{
    public function testStaticConstructor()
    {
        $this->assertInstanceOf(DockerAsync::class, DockerAsync::create());
    }

    public function testAsync(): void
    {
        $this->markTestSkipped('Need update of socket library');

        Loop::run(function () {
            $docker = DockerAsync::create();

            $containerConfig = new ContainersCreatePostBody();
            $containerConfig->setImage('busybox:latest');
            $containerConfig->setCmd(['echo', '-n', 'output']);
            $containerConfig->setAttachStdout(true);
            $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

            $containerCreate = yield $docker->containerCreate($containerConfig);
            $containerStart = yield $docker->containerStart($containerCreate->getId());
            $containerInfo = yield $docker->containerInspect($containerCreate->getId());

            $this->assertSame($containerCreate->getId(), $containerInfo->getId());
        });
    }
}
