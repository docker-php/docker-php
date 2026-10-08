<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\ContainerStats as BaseEndpoint;
use Docker\Stream\StatsStream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerStats extends BaseEndpoint
{
    protected function transformResponseBody(ResponseInterface $response, SerializerInterface $serializer, ?string $contentType = null)
    {
        // Both modes use application/json, so the request decides: `stream`
        // keeps the response open and sends one sample per document.
        if (200 === $response->getStatusCode() && $this->getQueryOptionsResolver()->resolve($this->queryParameters)['stream']) {
            return new StatsStream($response->getBody(), $serializer);
        }

        return parent::transformResponseBody($response, $serializer, $contentType);
    }
}
