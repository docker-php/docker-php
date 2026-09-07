<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\ContainerLogs as BaseEndpoint;
use Docker\Stream\DockerRawStream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerLogs extends BaseEndpoint
{
    /** @var callable|null Resolves the container's TTY setting for ambiguous raw-stream responses. */
    private $ttyResolver;

    public function __construct(string $id, array $queryParameters = [], array $accept = [], ?callable $ttyResolver = null)
    {
        parent::__construct($id, $queryParameters, $accept);
        $this->ttyResolver = $ttyResolver;
    }

    protected function transformResponseBody(ResponseInterface $response, SerializerInterface $serializer, ?string $contentType = null)
    {
        if (200 === $response->getStatusCode()) {
            $mediaType = strtolower(trim(explode(';', $contentType ?? '')[0]));
            if (DockerRawStream::MULTIPLEXED_HEADER === $mediaType) {
                return new DockerRawStream($response->getBody());
            }
            if (DockerRawStream::HEADER === $mediaType) {
                // Before API 1.42, this header also identifies framed non-TTY logs.
                // Keep the historical framed behavior when used without a resolver.
                $multiplexed = null === $this->ttyResolver || !($this->ttyResolver)();

                return new DockerRawStream($response->getBody(), $multiplexed);
            }
        }

        return parent::transformResponseBody($response, $serializer, $contentType);
    }
}
