<?php

declare(strict_types=1);

namespace Docker\Stream;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Live container resource usage from the stats endpoint.
 *
 * Callables passed to onFrame() receive each sample in the same form
 * containerStats() returns with `stream => false`: a ContainerStatsResponse
 * model from API 1.48, or a decoded \stdClass for earlier API versions.
 */
class StatsStream extends CallbackStream
{
    use ReadsJsonDocuments;

    private const MODEL = 'Docker\\API\\Model\\ContainerStatsResponse';

    public function __construct(StreamInterface $stream, private ?SerializerInterface $serializer = null)
    {
        parent::__construct($stream);
    }

    protected function readFrame()
    {
        $jsonFrame = $this->readJsonDocument();
        if (null === $jsonFrame) {
            return null;
        }

        if (null !== $this->serializer && class_exists(self::MODEL)) {
            return $this->serializer->deserialize($jsonFrame, self::MODEL, 'json');
        }

        return json_decode($jsonFrame, false, 512, \JSON_THROW_ON_ERROR);
    }
}
