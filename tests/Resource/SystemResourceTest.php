<?php

declare(strict_types=1);

namespace Docker\Tests\Resource;

use Docker\API\Model\EventsGetResponse200;
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

    public function testGetEvents(): void
    {
        $stream = $this->getManager()->systemEvents([
            'since' => (string) (\time() - 1),
            'until' => (string) (\time() + 4),
        ]);

        $lastEvent = null;

        $stream->onFrame(function ($event) use (&$lastEvent): void {
            $lastEvent = $event;
        });

        self::getDocker()->imageCreate('', [
            'fromImage' => 'busybox:latest',
        ]);

        $stream->wait();

        $this->assertInstanceOf(EventsGetResponse200::class, $lastEvent);
    }
}
