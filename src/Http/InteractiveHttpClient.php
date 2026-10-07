<?php

declare(strict_types=1);

namespace Docker\Http;

use Http\Client\Common\PluginClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Marks the factory's socket transport as capable of handing off a connection.
 *
 * @internal use DockerClientFactory::createInteractive() to construct this client
 */
final class InteractiveHttpClient implements ClientInterface
{
    public function __construct(private PluginClient $client)
    {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->client->sendRequest($request);
    }
}
