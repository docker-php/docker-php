<?php

declare(strict_types=1);

namespace Docker\Resource;

use Docker\API\Resource\SystemResourceTrait as BaseSystemResourceTrait;
use Docker\Docker;
use Docker\Stream\EventStream;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

trait SystemResourceTrait
{
    use BaseSystemResourceTrait {
        systemEvents as systemEventsLegacy;
    }

    public function systemEvents(array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        $response = $this->systemEventsLegacy($parameters, Docker::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new EventStream($response->getBody(), $this->serializer);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $eventList = [];

                $stream = new EventStream($response->getBody(), $this->serializer);
                $stream->onFrame(function ($event) use (&$eventList): void {
                    $eventList[] = $event;
                });
                $stream->wait();

                return $eventList;
            }
        }

        return $response;
    }
}
