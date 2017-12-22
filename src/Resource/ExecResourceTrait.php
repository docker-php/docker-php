<?php

declare(strict_types=1);

namespace Docker\Resource;

use Docker\Docker;
use Docker\Stream\DockerRawStream;
use Joli\Jane\OpenApi\Runtime\Client\QueryParam;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

trait ExecResourceTrait
{
    public function execStart(string $id, \Docker\API\Model\ExecIdStartPostBody $execStartConfig, array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url = '/exec/{id}/start';
        $url = \str_replace('{id}', \urlencode($id), $url);
        $url = $url.('?'.$queryParam->buildQueryString($parameters));
        $headers = \array_merge(['Host' => 'localhost', 'Content-Type' => 'application/json'], $queryParam->buildHeaders($parameters));
        $body = $this->serializer->serialize($execStartConfig, 'json');
        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if (200 === $response->getStatusCode() && DockerRawStream::HEADER === $response->getHeaderLine('Content-Type')) {
            if (Docker::FETCH_STREAM === $fetch) {
                return new DockerRawStream($response->getBody());
            }
        }

        if (Resource::FETCH_OBJECT === $fetch) {
            if (200 === $response->getStatusCode()) {
                return null;
            }
            if (404 === $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'Docker\\API\\Model\\ErrorResponse', 'json');
            }
            if (409 === $response->getStatusCode()) {
                return $this->serializer->deserialize((string) $response->getBody(), 'Docker\\API\\Model\\ErrorResponse', 'json');
            }
        }

        return $response;
    }
}
