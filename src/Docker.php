<?php

namespace Docker;

use Docker\API\Normalizer\NormalizerFactory;
use Docker\API\Resource\ContainerResource;
use Docker\API\Resource\ExecResource;
use Docker\API\Resource\ImageResource;
use Docker\API\Resource\MiscResource;
use Docker\API\Resource\NetworkResource;
use Docker\API\Resource\VolumeResource;
use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Joli\Jane\Encoder\RawEncoder;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Docker\Docker
 */
class Docker
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var MessageFactory
     */
    private $messageFactory;

    /**
     * @var ContainerResource
     */
    private $containerManager;

    /**
     * @var ImageResource
     */
    private $imageManager;

    /**
     * @var MiscResource
     */
    private $miscManager;

    /**
     * @var VolumeResource
     */
    private $volumeManager;

    /**
     * @var NetworkResource
     */
    private $networkManager;

    /**
     * @var ExecResource
     */
    private $execManager;

    /**
     * @param HttpClient $httpClient Http client to use with Docker
     */
    public function __construct(HttpClient $httpClient = null, Serializer $serializer = null, MessageFactory $messageFactory = null)
    {
        $this->httpClient = $httpClient ?: DockerClient::createFromEnv();

        if ($serializer === null) {
            $serializer = new Serializer(
                NormalizerFactory::create(),
                [
                    new JsonEncoder(
                        new JsonEncode(),
                        new JsonDecode()
                    ),
                    new RawEncoder()
                ]
            );
        }

        if ($messageFactory === null) {
            $messageFactory = new MessageFactory\GuzzleMessageFactory();
        }

        $this->serializer = $serializer;
        $this->messageFactory = $messageFactory;
    }

    /**
     * @return ContainerResource
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerResource($this->httpClient, $this->messageFactory, $this->serializer);
        }

        return $this->containerManager;
    }

    /**
     * @return ImageResource
     */
    public function getImageManager()
    {
        if (null === $this->imageManager) {
            $this->imageManager = new ImageResource($this->httpClient, $this->messageFactory, $this->serializer);
        }

        return $this->imageManager;
    }

    /**
     * @return MiscResource
     */
    public function getMiscManager()
    {
        if (null === $this->miscManager) {
            $this->miscManager = new MiscResource($this->httpClient, $this->messageFactory, $this->serializer);
        }

        return $this->miscManager;
    }

    /**
     * @return ExecResource
     */
    public function getExecManager()
    {
        if (null === $this->execManager) {
            $this->execManager = new ExecResource($this->httpClient, $this->messageFactory, $this->serializer);
        }

        return $this->execManager;
    }

    /**
     * @return VolumeResource
     */
    public function getVolumeManager()
    {
        if (null === $this->volumeManager) {
            $this->volumeManager = new VolumeResource($this->httpClient, $this->messageFactory, $this->serializer);
        }

        return $this->volumeManager;
    }

    /**
     * @return NetworkResource
     */
    public function getNetworkManager()
    {
        if (null === $this->networkManager) {
            $this->networkManager = new NetworkResource($this->httpClient, $this->messageFactory, $this->serializer);
        }

        return $this->networkManager;
    }
}
