<?php

declare(strict_types=1);

namespace Docker;

use Amp\Artax\Request;
use Amp\CancellationTokenSource;
use Amp\Promise;
use Docker\API\ClientAsync;
use Docker\Client\AmpArtaxStreamEndpoint;
use Docker\Client\ProvideAmpArtaxClientOptions;
use Docker\Endpoint\SystemEvents;
use Jane\OpenApiRuntime\Client\AmpArtaxEndpoint;
use function Amp\call;

/**
 * Docker\Docker.
 */
class DockerAsync extends ClientAsync
{
    public static function create($httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = DockerAsyncClient::createFromEnv();
        }

        return parent::create($httpClient);
    }

    /**
     * {@inheritdoc}
     */
    public function systemEvents(array $queryParameters = [], string $fetch = self::FETCH_OBJECT): Promise
    {
        return $this->executeArtaxEndpoint(new SystemEvents($queryParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function executeArtaxEndpoint(AmpArtaxEndpoint $endpoint, string $fetch = self::FETCH_OBJECT): Promise
    {
        return call(function () use ($endpoint, $fetch) {
            [$bodyHeaders, $body] = $endpoint->getBody($this->serializer);
            $queryString = $endpoint->getQueryString();
            $uri = '' !== $queryString ? $endpoint->getUri().'?'.$queryString : $endpoint->getUri();
            $request = new Request($uri, $endpoint->getMethod());
            $request = $request->withBody($body);
            $request = $request->withHeaders($endpoint->getHeaders($bodyHeaders));
            $options = [];
            if ($endpoint instanceof ProvideAmpArtaxClientOptions) {
                $options = $endpoint->getAmpArtaxClientOptions();
            }

            if ($endpoint instanceof AmpArtaxStreamEndpoint) {
                $cancellationTokenSource = new CancellationTokenSource();

                return $endpoint->parseArtaxStreamResponse(
                    yield $this->httpClient->request($request, $options, $cancellationTokenSource->getToken()),
                    $this->serializer,
                    $cancellationTokenSource,
                    $fetch
                );
            }

            return $endpoint->parseArtaxResponse(
                yield $this->httpClient->request($request, $options),
                $this->serializer,
                $fetch
            );
        });
    }
}
