<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\ContainerAttachWebsocket as BaseEndpoint;
use Docker\Stream\AttachWebsocketStream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerAttachWebsocket extends BaseEndpoint
{
    public function getExtraHeaders(): array
    {
        return array_merge(
            parent::getExtraHeaders(),
            [
                'Host' => 'localhost',
                'Origin' => 'php://docker-php',
                'Upgrade' => 'websocket',
                'Connection' => 'Upgrade',
                'Sec-WebSocket-Version' => '13',
                // RFC 6455 requires 16 random bytes.
                'Sec-WebSocket-Key' => base64_encode(random_bytes(16)),
            ]
        );
    }

    protected function transformResponseBody(ResponseInterface $response, SerializerInterface $serializer, ?string $contentType = null)
    {
        if (101 === $response->getStatusCode() && 'websocket' === strtolower(trim($response->getHeaderLine('Upgrade')))) {
            return new AttachWebsocketStream($response->getBody());
        }

        return parent::transformResponseBody($response, $serializer, $contentType);
    }
}
