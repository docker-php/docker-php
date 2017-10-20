<?php

namespace Docker;

use Http\Client\HttpClient;
use Http\Message\MessageFactory;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

/**
 * Docker\Docker
 */
class Docker
{
    const VERSION_1_25 = 'V1_25';
    const VERSION_1_26 = 'V1_26';
    const VERSION_1_27 = 'V1_27';
    const VERSION_1_28 = 'V1_28';
    const VERSION_1_29 = 'V1_29';
    const VERSION_1_30 = 'V1_30';
    const VERSION_1_31 = 'V1_31';
    const VERSION_1_32 = 'V1_32';
    const VERSION_1_33 = 'V1_33';
    const VERSION_LAST_STABLE = self::VERSION_1_32;

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
     * @var array
     */
    private $resources = [];

    /**
     * @var string
     */
    private $version;

    /**
     * @param HttpClient|null     $httpClient     Http client to use with Docker
     * @param Serializer|null     $serializer     Deserialize docker response into php objects
     * @param MessageFactory|null $messageFactory How to create docker request (in PSR7)
     */
    public function __construct(HttpClient $httpClient, Serializer $serializer, MessageFactory $messageFactory, string $version)
    {
        $this->httpClient = $httpClient;
        $this->serializer = $serializer;
        $this->messageFactory = $messageFactory;
        $this->version = $version;

    }

    /**
     * @return \Docker\API\V1_33\Resource\ConfigResource
     */
    public function config()
    {
        return $this->getResource('ConfigResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\ContainerResource
     */
    public function container()
    {
        return $this->getResource('ContainerResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\DefaultResource
     */
    public function other()
    {
        return $this->getResource('DefaultResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\DistributionResource
     */
    public function distribution()
    {
        return $this->getResource('DistributionResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\ExecResource
     */
    public function exec()
    {
        return $this->getResource('ExecResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\ImageResource
     */
    public function image()
    {
        return $this->getResource('ImageResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\NetworkResource
     */
    public function network()
    {
        return $this->getResource('NetworkResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\NodeResource
     */
    public function node()
    {
        return $this->getResource('NodeResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\PluginResource
     */
    public function plugin()
    {
        return $this->getResource('PluginResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\SecretResource
     */
    public function secret()
    {
        return $this->getResource('SecretResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\ServiceResource
     */
    public function service()
    {
        return $this->getResource('ServiceResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\SessionExperimentalResource
     */
    public function session()
    {
        return $this->getResource('SessionExperimentalResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\SwarmResource
     */
    public function swarm()
    {
        return $this->getResource('SwarmResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\SystemResource
     */
    public function system()
    {
        return $this->getResource('SystemResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\TaskResource
     */
    public function task()
    {
        return $this->getResource('TaskResource');
    }

    /**
     * @return \Docker\API\V1_33\Resource\VolumeResource
     */
    public function volume()
    {
        return $this->getResource('VolumeResource');
    }

    private function getResource($class)
    {
        if (array_key_exists($class, $this->resources)) {
            return $this->resources[$class];
        }

        $generatedResourceClass = '\\Docker\\API\\' . $this->version .  '\\Resource\\' . $class;
        $resourceClass = '\\Docker\\Resource\\' . $class;

        if (!class_exists($resourceClass)) {
            $resourceClass = $generatedResourceClass;
        }

        $this->resources[$class] = new $resourceClass($this->httpClient, $this->messageFactory, $this->serializer, $generatedResourceClass, $this->version);

        return $this->resources[$class];
    }

    /**
     * Create a new docker api client
     *
     * @param string              $version
     * @param HttpClient|null     $httpClient
     * @param Serializer|null     $serializer
     * @param MessageFactory|null $messageFactory
     *
     * @return static
     */
    public static function create(string $version = self::VERSION_LAST_STABLE, HttpClient $httpClient = null, Serializer $serializer = null, MessageFactory $messageFactory = null)
    {
        $httpClient = $httpClient ?: DockerClient::createFromEnv();

        if ($serializer === null) {
            $normalizerClass = 'Docker\\API\\' . $version . '\\Normalizer\\NormalizerFactory';

            $serializer = new Serializer(
                $normalizerClass::create(),
                [
                    new JsonEncoder(
                        new JsonEncode(),
                        new JsonDecode()
                    ),
                ]
            );
        }

        if ($messageFactory === null) {
            $messageFactory = new MessageFactory\GuzzleMessageFactory();
        }

        return new static($httpClient, $serializer, $messageFactory, $version);
    }
}
