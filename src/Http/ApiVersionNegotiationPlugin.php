<?php

declare(strict_types=1);

namespace Docker\Http;

use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Promise\Promise;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriFactoryInterface;

/**
 * Prefix requests with the lower of the client's and the daemon's API version.
 *
 * Before the first request, an unversioned /_ping reads the daemon's maximum
 * from its API-Version header, as the Docker CLI does. Older daemons then keep
 * working with a newer API package; requests never ask for a version the
 * installed models do not describe.
 */
final class ApiVersionNegotiationPlugin implements Plugin
{
    private ?string $version = null;

    public function __construct(
        private readonly string $clientVersion,
        private readonly UriFactoryInterface $uriFactory,
        private readonly RequestFactoryInterface $requestFactory,
    ) {
    }

    /** The negotiated version, or null before the first request. */
    public function getNegotiatedVersion(): ?string
    {
        return $this->version;
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        $version = $this->version ?? $this->negotiate($next);

        return (new AddPathPlugin($this->uriFactory->createUri('/v'.$version)))->handleRequest($request, $next, $first);
    }

    private function negotiate(callable $next): string
    {
        // A connection error propagates, so an unreachable daemon is not tried
        // twice. An error response without the header keeps the client version.
        $response = $next($this->requestFactory->createRequest('GET', '/_ping'))->wait();
        $daemonVersion = trim($response->getHeaderLine('API-Version'));
        $response->getBody()->close();

        if (!preg_match('/^[0-9]+\.[0-9]+$/D', $daemonVersion)) {
            return $this->version = $this->clientVersion;
        }

        return $this->version = version_compare($daemonVersion, $this->clientVersion, '<') ? $daemonVersion : $this->clientVersion;
    }
}
