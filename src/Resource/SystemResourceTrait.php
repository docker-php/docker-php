<?php

namespace Docker\Resource;

use Docker\Docker;
use Docker\Stream\EventStream;
use Joli\Jane\OpenApi\Runtime\Client\Resource;
use Docker\API\Resource\SystemResourceTrait as BaseSystemResourceTrait;

trait SystemResourceTrait
{
    use BaseSystemResourceTrait {
        systemEvents as systemEventsLegacy;
    }

    public function systemEvents($parameters = [], $fetch = Docker::FETCH_OBJECT)
    {
        $response = $this->systemEventsLegacy($parameters, Docker::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new EventStream($response->getBody(), $this->serializer);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $eventList = [];

                $stream = new EventStream($response->getBody(), $this->serializer);
                $stream->onFrame(function ($event) use (&$eventList) {
                    $eventList[] = $event;
                });
                $stream->wait();

                return $eventList;
            }
        }

        return $response;
    }
}