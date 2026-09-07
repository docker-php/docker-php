<?php

declare(strict_types=1);

namespace Docker;

use Docker\API\Client;
use Docker\API\Endpoint\SystemInfo;
use Docker\API\Model\AuthConfig;
use Docker\API\Model\ExecIdStartPostBody;
use Docker\Endpoint\ContainerAttach;
use Docker\Endpoint\ContainerAttachWebsocket;
use Docker\Endpoint\ContainerLogs;
use Docker\Endpoint\ExecStart;
use Docker\Endpoint\ImageBuild;
use Docker\Endpoint\ImageCreate;
use Docker\Endpoint\ImagePush;
use Docker\Endpoint\SystemEvents;
use Docker\Exception\BadRequestException;

/**
 * Docker\Docker.
 */
class Docker extends Client
{
    /**
     * {@inheritdoc}
     */
    public function containerAttach(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ContainerAttach($id, $queryParameters, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function containerAttachWebsocket(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ContainerAttachWebsocket($id, $queryParameters, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function containerLogs(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ContainerLogs(
            $id,
            $queryParameters,
            $accept,
            fn (): bool => (bool) $this->containerInspect($id)->getConfig()?->getTty()
        ), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function execStart(string $id, ?ExecIdStartPostBody $requestBody = null, string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ExecStart($id, $requestBody, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function imageBuild($requestBody = null, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executeEndpoint(new ImageBuild($requestBody, $queryParameters, $headerParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function imageCreate(?string $requestBody = null, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executeEndpoint(new ImageCreate($requestBody, $queryParameters, $headerParameters), $fetch);
    }
    public function imagePush(string $name, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        if (isset($headerParameters['X-Registry-Auth']) && $headerParameters['X-Registry-Auth'] instanceof AuthConfig) {
            $headerParameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($headerParameters['X-Registry-Auth'], 'json'));
        }

        return $this->executeEndpoint(new ImagePush($name, $queryParameters, $headerParameters, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     */
    public function systemEvents(array $queryParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executeEndpoint(new SystemEvents($queryParameters), $fetch);
    }

    public static function create(
        $httpClient = null,
        array $additionalPlugins = [],
        array $additionalNormalizers = [],
        bool $applyServerPlugins = true
    ): self
    {
        if (null === $httpClient) {
            $httpClient = DockerClientFactory::createFromEnv();
            $applyServerPlugins = false;
        }

        $client = parent::create($httpClient, $additionalPlugins, $additionalNormalizers, $applyServerPlugins);
        $response = $client->executeRawEndpoint(new SystemInfo());
        $testClient = $response->getBody()->getContents();
        $jsonObj = json_decode($testClient);

        if ($jsonObj !== null) {
            if (isset($jsonObj->message)) {
                // Check if the client is too new
                if (strpos($jsonObj->message, 'client version') !== false && strpos($jsonObj->message, 'is too new') !== false) {
                    throw new BadRequestException("The client version is not supported by your version of Docker. Message: {$jsonObj->message}", $response);
                } else {
                    throw new BadRequestException($jsonObj->message, $response);
                }
            }
        } else {
            throw new BadRequestException("Failed to decode JSON.", $response);
        }
        return $client;
    }
}
