<?php

namespace Docker\API\Resource;

use Joli\Jane\Swagger\Client\QueryParam;
use Joli\Jane\Swagger\Client\Resource;

class VolumeResource extends Resource
{
    /**
     * List volumes.
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function findAll($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('filters', null);
        $url      = sprintf('/v1.21/volumes?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\VolumeList', 'json');
        }

        return $response;
    }

    /**
     * Create a volume.
     * 
     * @param mixed  $volumeConfig Volume configuration
     * @param array  $parameters   List of parameters
     * @param string $fetch        Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create($volumeConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/volumes/create?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $volumeConfig);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('201' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Volume', 'json');
        }

        return $response;
    }

    /**
     * Instruct the driver to remove the volume.
     * 
     * @param mixed  $name       Volume name or id
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function remove($name, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/volumes/%s?%s', $name, $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('DELETE', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Inspect a volume.
     * 
     * @param mixed  $name       Volume name or id
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function inspect($name, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/volumes/%s?%s', $name, $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Volume', 'json');
        }

        return $response;
    }
}
