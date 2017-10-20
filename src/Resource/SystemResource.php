<?php

namespace Docker\Resource;

use Docker\Stream\EventStream;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

class SystemResource extends OverrideResource
{
    public function systemEvents($parameters = [], $fetch = Resource::FETCH_OBJECT)
    {
        $response = $this->resource->systemEvents($parameters, Resource::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new EventStream($response->getBody(), $this->serializer, $this->version);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $eventList = [];

                $stream = new EventStream($response->getBody(), $this->serializer, $this->version);
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