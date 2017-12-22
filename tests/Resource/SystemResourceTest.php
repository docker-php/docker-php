<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\EventsGetResponse200;
use Docker\Docker;
use Docker\Tests\TestCase;

class SystemResourceTest extends TestCase
{
    /**
     * Return a container manager.
     */
    private function getManager()
    {
        return self::getDocker();
    }

    public function testGetEventsStream(): void
    {
        $stream = $this->getManager()->systemEvents([
            'since' => \time() - 1,
            'until' => \time() + 4,
        ], Docker::FETCH_STREAM);
        $lastEvent = null;

        $stream->onFrame(function ($event) use (&$lastEvent): void {
            $lastEvent = $event;
        });

        self::getDocker()->imageCreate(null, [
            'fromImage' => 'busybox:latest',
        ]);
        $stream->wait();

        $this->assertInstanceOf(EventsGetResponse200::class, $lastEvent);
    }

    public function testGetEventsObject(): void
    {
        $events = $this->getManager()->systemEvents([
            'since' => \time() - (60 * 60 * 24),
            'until' => \time(),
        ], Docker::FETCH_OBJECT);

        $this->assertInternalType('array', $events);
        $this->assertInstanceOf(EventsGetResponse200::class, $events[0]);
    }
}
