<?php

namespace Docker\Resource;

use Docker\Stream\AttachWebsocketStream;
use Docker\Stream\DockerRawStream;
use Joli\Jane\OpenApi\Runtime\Client\QueryParam;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

/**
 * Override some generated functions to allow custom streams
 */
class ContainerResource extends OverrideResource
{
    public function containerAttach($id, $parameters = [], $fetch = self::FETCH_STREAM)
    {
        $response = $this->resource->containerAttach($id, $parameters, Resource::FETCH_RESPONSE);

        if ($response->getStatusCode() == 200 && DockerRawStream::HEADER == $response->getHeaderLine('Content-Type')) {
            if ($fetch == self::FETCH_STREAM) {
                return new DockerRawStream($response->getBody());
            }
        }

        return $response;
    }

    public function containerAttachWebsocket($id, $parameters = [], $fetch = self::FETCH_STREAM)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('logs', null);
        $queryParam->setDefault('stream', null);
        $queryParam->setDefault('stdin', null);
        $queryParam->setDefault('stdout', null);
        $queryParam->setDefault('stderr', null);

        $url      = '/containers/{id}/attach/ws';
        $url      = str_replace('{id}', $id, $url);
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));

        $headers  = array_merge([
            'Host' => 'localhost',
            'Origin' => 'php://docker-php',
            'Upgrade' => 'websocket',
            'Connection' => 'Upgrade',
            'Sec-WebSocket-Version' => '13',
            'Sec-WebSocket-Key' => base64_encode(uniqid()),
        ], $queryParam->buildHeaders($parameters));

        $body     = $queryParam->buildFormDataString($parameters);

        $request  = $this->messageFactory->createRequest('GET', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() == 101) {
            if ($fetch == self::FETCH_STREAM) {
                return new AttachWebsocketStream($response->getBody());
            }
        }

        return $response;
    }

    public function containerLogs($id, $parameters = [], $fetch = Resource::FETCH_OBJECT) {
        $response = $this->resource->containerLogs($id, $parameters, Resource::FETCH_RESPONSE);

        if ($response->getStatusCode() == 200) {
            if ($fetch == self::FETCH_STREAM) {
                return new DockerRawStream($response->getBody());
            }

            if ($fetch == Resource::FETCH_OBJECT) {
                $dockerRawStream = new DockerRawStream($response->getBody());

                $logs = [
                    'stdout' => [],
                    'stderr' => []
                ];

                $dockerRawStream->onStdout(function ($logLine) use (&$logs) {
                    $logs['stdout'][] = $logLine;
                });
                $dockerRawStream->onStderr(function ($logLine) use (&$logs) {
                    $logs['stderr'][] = $logLine;
                });

                $dockerRawStream->wait();

                return $logs;
            }
        }

        return $response;
    }
}