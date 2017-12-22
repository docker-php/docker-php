<?php

declare(strict_types=1);

namespace Docker\Resource;

use Docker\API\Resource\ContainerResourceTrait as BaseContainerResourceTrait;
use Docker\Docker;
use Docker\Stream\AttachWebsocketStream;
use Docker\Stream\DockerRawStream;
use Joli\Jane\OpenApi\Runtime\Client\QueryParam;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

/**
 * Override some generated functions to allow custom streams.
 */
trait ContainerResourceTrait
{
    use BaseContainerResourceTrait {
        containerAttach as containerAttachLegacy;
        containerLogs as containerLogsLegacy;
    }

    public function containerAttach(string $id, array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        $response = $this->containerAttachLegacy($id, $parameters, Docker::FETCH_RESPONSE);

        if (200 === $response->getStatusCode() && DockerRawStream::HEADER === $response->getHeaderLine('Content-Type')) {
            if (Resource::FETCH_OBJECT === $fetch) {
                return new DockerRawStream($response->getBody());
            }
        }

        return $response;
    }

    public function containerAttachWebsocket(string $id, array $parameters = [], string $fetch = self::FETCH_STREAM)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('logs', null);
        $queryParam->setDefault('stream', null);
        $queryParam->setDefault('stdin', null);
        $queryParam->setDefault('stdout', null);
        $queryParam->setDefault('stderr', null);

        $url = '/containers/{id}/attach/ws';
        $url = \str_replace('{id}', $id, $url);
        $url = $url.('?'.$queryParam->buildQueryString($parameters));

        $headers = \array_merge([
            'Host' => 'localhost',
            'Origin' => 'php://docker-php',
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Version' => '13',
            'Sec-WebSocket-Key' => \base64_encode(\uniqid()),
        ], $queryParam->buildHeaders($parameters));

        $body = $queryParam->buildFormDataString($parameters);

        $request = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if (101 === $response->getStatusCode()) {
            if (Docker::FETCH_STREAM === $fetch) {
                return new AttachWebsocketStream($response->getBody());
            }
        }

        return $response;
    }

    public function containerLogs(string $id, array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        $response = $this->containerLogsLegacy($id, $parameters, Docker::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (Docker::FETCH_STREAM === $fetch) {
                return new DockerRawStream($response->getBody());
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $dockerRawStream = new DockerRawStream($response->getBody());

                $logs = [
                    'stdout' => [],
                    'stderr' => [],
                ];

                $dockerRawStream->onStdout(function ($logLine) use (&$logs): void {
                    $logs['stdout'][] = $logLine;
                });
                $dockerRawStream->onStderr(function ($logLine) use (&$logs): void {
                    $logs['stderr'][] = $logLine;
                });

                $dockerRawStream->wait();

                return $logs;
            }
        }

        return $response;
    }
}
