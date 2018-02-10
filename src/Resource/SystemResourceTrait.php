<?php

declare(strict_types=1);

namespace Docker\Resource;

use Docker\API\Endpoint\SystemEvents;
use Docker\Docker;
use Docker\Stream\EventStream;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

trait SystemResourceTrait
{
    /**
     * @see \Docker\API\Resource\SystemResourceTrait::systemEvents
     * {@inheritdoc}
     */
    public function systemEvents(array $queryParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        $endpoint = new SystemEvents($queryParameters);
        $response = $this->executePsr7Endpoint($endpoint, Docker::FETCH_RESPONSE);

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

        if (Resource::FETCH_OBJECT === $fetch) {
            return $endpoint->transformResponseBody((string) $response->getBody(), $response->getStatusCode(), $this->serializer);
        }

        return $response;
    }
}
