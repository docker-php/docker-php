<?php

declare(strict_types=1);

namespace Docker\Stream;

use Psr\Http\Message\StreamInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Represent a stream that decode a stream with multiple json in it.
 */
abstract class MultiJsonStream extends CallbackStream
{
    use ReadsJsonDocuments;

    /** @var SerializerInterface Serializer to decode incoming json object */
    private $serializer;

    public function __construct(StreamInterface $stream, SerializerInterface $serializer)
    {
        parent::__construct($stream);

        $this->serializer = $serializer;
    }

    protected function readFrame()
    {
        $jsonFrame = $this->readJsonDocument();
        if (null === $jsonFrame) {
            return null;
        }

        return $this->serializer->deserialize($jsonFrame, 'Docker\\API\\Model\\'.$this->getDecodeClass(), 'json');
    }

    /**
     * Get the decode class to pass to serializer.
     *
     * @return string
     */
    abstract protected function getDecodeClass();
}
