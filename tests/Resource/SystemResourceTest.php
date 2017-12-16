<?php

namespace Docker\Tests\Resource;

use Docker\API\Model\Event;
use Docker\API\Model\EventsGetResponse200;
use Docker\Docker;
use Docker\Manager\MiscManager;
use Docker\Resource\SystemResource;
use Docker\Tests\TestCase;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

class SystemResourceTest extends TestCase
{
    /**
     * Return a container manager
     */
    private function getManager()
    {
        return self::getDocker();
    }

    public function testGetEventsStream()
    {
        $stream = $this->getManager()->systemEvents([
            'since' => time() - 1,
            'until' => time() + 4
        ], Docker::FETCH_STREAM);
        $lastEvent = null;

        $stream->onFrame(function ($event) use (&$lastEvent) {
            $lastEvent = $event;
        });

        self::getDocker()->imageCreate(null, [
            'fromImage' => 'busybox:latest'
        ]);
        $stream->wait();

        $this->assertInstanceOf(EventsGetResponse200::class, $lastEvent);
    }

    public function testGetEventsObject()
    {
        $events = $this->getManager()->systemEvents([
            'since' => time() - (60 * 60 * 24),
            'until' => time()
        ], Docker::FETCH_OBJECT);

        $this->assertInternalType('array', $events);
        $this->assertInstanceOf(EventsGetResponse200::class, $events[0]);
    }
}
