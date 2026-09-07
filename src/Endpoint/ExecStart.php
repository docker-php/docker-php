<?php

declare(strict_types=1);

namespace Docker\Endpoint;

use Docker\API\Endpoint\ExecStart as BaseEndpoint;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Stream\DockerRawStream;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ExecStart extends BaseEndpoint
{
    protected function transformResponseBody(ResponseInterface $response, SerializerInterface $serializer, ?string $contentType = null)
    {
        if (200 === $response->getStatusCode() || 101 === $response->getStatusCode()) {
            $mediaType = strtolower(trim(explode(';', $contentType ?? '')[0]));
            if (DockerRawStream::MULTIPLEXED_HEADER === $mediaType) {
                return new DockerRawStream($response->getBody());
            }
            if (DockerRawStream::HEADER === $mediaType) {
                // Non-upgraded and older API responses use this header for both modes.
                $tty = $this->body instanceof ExecIdStartPostBody && $this->body->getTty();

                return new DockerRawStream($response->getBody(), !$tty);
            }
        }

        return parent::transformResponseBody($response, $serializer, $contentType);
    }
}
