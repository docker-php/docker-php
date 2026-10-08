<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\SystemEvents as BaseEndpoint;
use Docker\Stream\EventStream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SystemEvents extends BaseEndpoint
{
    /**
     * @param array $accept Accept header values; API 1.52 added this parameter
     */
    public function __construct(array $queryParameters = [], array $accept = [])
    {
        parent::__construct($queryParameters);
        // From API 1.52 the generated endpoint stores the Accept values here.
        if (property_exists($this, 'accept')) {
            $this->accept = $accept;
        }
    }

    protected function transformResponseBody(ResponseInterface $response, SerializerInterface $serializer, ?string $contentType = null)
    {
        if (200 === $response->getStatusCode()) {
            return new EventStream($response->getBody(), $serializer);
        }

        return parent::transformResponseBody($response, $serializer, $contentType);
    }
}
