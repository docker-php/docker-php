<?php

namespace Docker\Helper;

use Docker\Context\ContextInterface;
use Docker\Exception\DockerException;
use Docker\Manager\ImageManager;
use Docker\Stream\BuildStream;
use Http\Message\StreamFactory;

/**
 * Helper for building image with a context
 */
class BuilderHelper
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var StreamFactory
     */
    protected $streamFactory;

    public function __construct(ImageManager $imageManager, StreamFactory $streamFactory)
    {
        $this->imageManager = $imageManager;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Build an image
     *
     * @param ContextInterface $context
     * @param string           $name
     * @param array            $parameters
     *
     * @return BuildStream Return a stream which can be waited and listened to have build
     *
     * @throws DockerException
     */
    public function build(ContextInterface $context, $name, $parameters = [])
    {
        $stream = $this->streamFactory->createStream($context->read());
        $buildResponse = $this->imageManager->build($stream, array_merge($parameters, [
            't' => $name,
            'q' => 'false'
        ]), ImageManager::FETCH_RESPONSE);

        if (200 !== $buildResponse->getStatusCode()) {
            throw new DockerException(sprintf('Build failed with status code %s', $buildResponse->getStatusCode()));
        }

        return new BuildStream($buildResponse->getBody());
    }
}
