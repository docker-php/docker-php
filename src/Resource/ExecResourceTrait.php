<?php

namespace Docker\Resource;

use Docker\Docker;
use Docker\Stream\DockerRawStream;
use Joli\Jane\OpenApi\Runtime\Client\QueryParam;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

trait ExecResourceTrait
{
    public function execStart($id, $execStartConfig, $parameters = [], $fetch = Docker::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/exec/{id}/start';
        $url        = str_replace('{id}', urlencode($id), $url);
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost', 'Content-Type' => 'application/json'], $queryParam->buildHeaders($parameters));
        $body       = $this->serializer->serialize($execStartConfig, 'json');
        $request    = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if ($response->getStatusCode() === 200 && DockerRawStream::HEADER === $response->getHeaderLine('Content-Type')) {
            if ($fetch === Docker::FETCH_STREAM) {
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