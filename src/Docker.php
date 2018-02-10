<?php

declare(strict_types=1);

namespace Docker;

use Docker\API\Client;
use Docker\API\Model\AuthConfig;
use Docker\Endpoint\ContainerAttach;
use Docker\Endpoint\ContainerAttachWebsocket;
use Docker\Endpoint\ContainerLogs;
use Docker\Endpoint\ExecStart;
use Docker\Endpoint\ImageBuild;
use Docker\Endpoint\ImageCreate;
use Docker\Endpoint\ImagePush;
use Docker\Endpoint\SystemEvents;

/**
 * Docker\Docker.
 */
class Docker extends Client
{
    /**
     * {@inheritdoc}
     */
    public function containerAttach(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new ContainerAttach($id, $queryParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function containerAttachWebsocket(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new ContainerAttachWebsocket($id, $queryParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function containerLogs(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new ContainerLogs($id, $queryParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function execStart(string $id, \Docker\API\Model\ExecIdStartPostBody $execStartConfig, string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new ExecStart($id, $execStartConfig), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function imageBuild($inputStream, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new ImageBuild($inputStream, $queryParameters, $headerParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function imageCreate(string $inputImage, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new ImageCreate($inputImage, $queryParameters, $headerParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function imagePush(string $name, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        if (isset($headerParameters['X-Registry-Auth']) && $headerParameters['X-Registry-Auth'] instanceof AuthConfig) {
            $headerParameters['X-Registry-Auth'] = \base64_encode($this->serializer->serialize($headerParameters['X-Registry-Auth'], 'json'));
        }

        return $this->executePsr7Endpoint(new ImagePush($name, $queryParameters, $headerParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function systemEvents(array $queryParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executePsr7Endpoint(new SystemEvents($queryParameters), $fetch);
    }

    public static function create($httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = DockerClientFactory::createFromEnv();
        }

        return parent::create($httpClient);
    }
}
