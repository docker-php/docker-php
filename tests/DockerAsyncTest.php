<?php

declare(strict_types=1);

namespace Docker\Tests;

use Amp\Delayed;
use Amp\Loop;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\EventsGetResponse200;
use Docker\DockerAsync;
use Docker\Stream\ArtaxCallbackStream;

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

    public function testSystemEventsAllowTheConsumptionOfDockerEvents(): void
    {
        Loop::run(function () {
            $docker = DockerAsync::create();

            $receivedEvents = [];
            /** @var ArtaxCallbackStream $events */
            $events = yield $docker->systemEvents(
                [
                    'filters' => json_encode(['type' => ['container'], 'action' => ['create']])
                ]
            );
            $events->onFrame(function ($event) use (&$receivedEvents): void {
                $receivedEvents[] = $event;
            });
            $events->listen();

            $containerConfig = new ContainersCreatePostBody();
            $containerConfig->setImage('busybox:latest');
            $containerConfig->setCmd(['echo', '-n', 'output']);

            $containerCreate = yield $docker->containerCreate($containerConfig);

            // Let a chance for the container create event to be dispatched to the consumer
            yield new Delayed(1000);

            $events->cancel();

            $this->assertCount(1, $receivedEvents);
            $this->assertInstanceOf(EventsGetResponse200::class, current($receivedEvents));
            $this->assertSame(current($receivedEvents)->getActor()->getId(), $containerCreate->getId());
        });
    }
}
