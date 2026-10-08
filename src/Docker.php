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
use Docker\Endpoint\InteractiveExecStart;
use Docker\Endpoint\SystemEvents;
use Docker\Exception\BadRequestException;
use Docker\Http\InteractiveHttpClient;
use Docker\Stream\AttachWebsocketStream;
use Docker\Stream\BuildStream;
use Docker\Stream\CreateImageStream;
use Docker\Stream\DockerRawStream;
use Docker\Stream\EventStream;
use Docker\Stream\InteractiveExecStream;
use Docker\Stream\PushStream;
use Docker\Stream\SocketReadStream;
use Http\Client\Socket\Stream as SocketStream;
use Psr\Http\Message\ResponseInterface;

/**
 * Docker\Docker.
 */
class Docker extends Client
{
    private bool $interactiveExecEnabled = false;

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? DockerRawStream|null : ResponseInterface)
     */
    public function containerAttach(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ContainerAttach($id, $queryParameters, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? AttachWebsocketStream|null : ResponseInterface)
     */
    public function containerAttachWebsocket(string $id, array $queryParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ContainerAttachWebsocket($id, $queryParameters, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? DockerRawStream|null : ResponseInterface)
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
     *
     * @return ($fetch is 'object' ? DockerRawStream|null : ResponseInterface)
     */
    public function execStart(string $id, ?ExecIdStartPostBody $requestBody = null, string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        return $this->executeEndpoint(new ExecStart($id, $requestBody, $accept), $fetch);
    }

    /**
     * Start attached exec with nonblocking stdin/stdout/stderr. timeoutMs is a
     * streaming deadline after upgrade; HTTP setup uses the factory timeout.
     * Inspect the exec separately after output EOF to obtain its exit status.
     */
    public function execStartInteractive(string $id, ?ExecIdStartPostBody $requestBody = null, ?int $timeoutMs = null): InteractiveExecStream
    {
        if (!$this->interactiveExecEnabled) {
            throw new \LogicException('Interactive exec requires DockerClientFactory::createInteractive() or the default socket client.');
        }
        if (null !== $timeoutMs && ($timeoutMs < 1 || $timeoutMs > 86400000)) {
            throw new \InvalidArgumentException('Interactive exec timeout must be between 1 and 86400000 milliseconds.');
        }
        if ('' === $id) {
            throw new \InvalidArgumentException('An exec ID is required.');
        }
        $body = null === $requestBody ? new ExecIdStartPostBody() : clone $requestBody;
        if ($body->getDetach()) {
            throw new \InvalidArgumentException('Interactive exec cannot be detached.');
        }
        $body->setDetach(false);
        $response = $this->executeRawEndpoint(new InteractiveExecStart($id, $body));
        $stream = $response->getBody();
        $mediaType = strtolower(trim(explode(';', $response->getHeaderLine('Content-Type'))[0]));
        if (101 !== $response->getStatusCode()
            || 'tcp' !== strtolower(trim($response->getHeaderLine('Upgrade')))
            || !preg_match('/(?:^|,)\s*upgrade\s*(?:,|$)/i', $response->getHeaderLine('Connection'))
            || $response->hasHeader('Transfer-Encoding')
            || $response->hasHeader('Content-Encoding')
            || !\in_array($mediaType, [DockerRawStream::HEADER, DockerRawStream::MULTIPLEXED_HEADER], true)
            || (!$stream instanceof SocketReadStream && !$stream instanceof SocketStream)) {
            $stream->close();
            throw new \RuntimeException('Docker did not provide a supported interactive exec upgrade (HTTP '.$response->getStatusCode().').');
        }
        // The detached resource keeps bytes PHP read ahead with the headers.
        $socket = $stream->detach();
        try {
            return new InteractiveExecStream($socket, !(bool) $body->getTty(), $timeoutMs);
        } catch (\Throwable $error) {
            if (\is_resource($socket)) {
                fclose($socket);
            }
            throw $error;
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? BuildStream|null : ResponseInterface)
     */
    public function imageBuild($requestBody = null, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executeEndpoint(new ImageBuild($requestBody, $queryParameters, $headerParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? CreateImageStream|null : ResponseInterface)
     */
    public function imageCreate(?string $requestBody = null, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT)
    {
        return $this->executeEndpoint(new ImageCreate($requestBody, $queryParameters, $headerParameters), $fetch);
    }

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? PushStream|null : ResponseInterface)
     */
    public function imagePush(string $name, array $queryParameters = [], array $headerParameters = [], string $fetch = self::FETCH_OBJECT, array $accept = [])
    {
        if (isset($headerParameters['X-Registry-Auth']) && $headerParameters['X-Registry-Auth'] instanceof AuthConfig) {
            $headerParameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($headerParameters['X-Registry-Auth'], 'json'));
        }

        return $this->executeEndpoint(new ImagePush($name, $queryParameters, $headerParameters, $accept), $fetch);
    }

    /**
     * {@inheritdoc}
     *
     * @return ($fetch is 'object' ? EventStream|null : ResponseInterface)
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
    ): self {
        if (null === $httpClient) {
            $httpClient = DockerClientFactory::createInteractiveFromEnv();
            $applyServerPlugins = false;
        }

        $client = parent::create($httpClient, $additionalPlugins, $additionalNormalizers, $applyServerPlugins);
        $client->interactiveExecEnabled = $httpClient instanceof InteractiveHttpClient;
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
