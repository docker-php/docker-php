<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Amp\Artax\Client as ArtaxClient;
use Docker\API\Endpoint\SystemEvents as BaseEndpoint;
use Docker\Client\AmpArtaxStreamEndpoint;
use Docker\Client\AmpArtaxStreamEndpointTrait;
use Docker\Client\ProvideAmpArtaxClientOptions;
use Docker\Stream\EventStream;
use Jane\OpenApiRuntime\Client\Client;
use Jane\OpenApiRuntime\Client\Exception\InvalidFetchModeException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class SystemEvents extends BaseEndpoint implements ProvideAmpArtaxClientOptions, AmpArtaxStreamEndpoint
{
    use AmpArtaxStreamEndpointTrait;

    public function getAmpArtaxClientOptions(): array
    {
        return [ArtaxClient::OP_TRANSFER_TIMEOUT => 0];
    }

    public function parsePSR7Response(ResponseInterface $response, SerializerInterface $serializer, string $fetchMode = Client::FETCH_OBJECT)
    {
        if (Client::FETCH_OBJECT === $fetchMode) {
            if (200 === $response->getStatusCode()) {
                return new EventStream($response->getBody(), $serializer);
            }

            return $this->transformResponseBody((string) $response->getBody(), $response->getStatusCode(), $serializer);
        }

        if (Client::FETCH_RESPONSE === $fetchMode) {
            return $response;
        }

        throw new InvalidFetchModeException(\sprintf('Fetch mode %s is not supported', $fetchMode));
    }
}
