<?php

namespace Docker\Tests\Resource;

use Docker\API\Model\Event;
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
        return self::getDocker()->system();
    }

    public function testGetEventsStream()
    {
        $stream = $this->getManager()->systemEvents([
            'since' => time() - 1,
            'until' => time() + 4
        ], SystemResource::FETCH_STREAM);
        $lastEvent = null;

        $stream->onFrame(function ($event) use (&$lastEvent) {
            $lastEvent = $event;
        });

        $this->getDocker()->image()->imageCreate(null, [
            'fromImage' => 'busybox:latest'
        ]);
        $stream->wait();

        $this->assertInstanceOf('Docker\\API\\'.self::getVersion().'\\Model\\EventsResponse200', $lastEvent);
    }

    public function testGetEventsObject()
    {
        $events = $this->getManager()->systemEvents([
            'since' => time() - (60 * 60 * 24),
            'until' => time()
        ], Resource::FETCH_OBJECT);

        $this->assertInternalType('array', $events);
        $this->assertInstanceOf('Docker\\API\\'.self::getVersion().'\\Model\\EventsResponse200', $events[0]);
    }
}
