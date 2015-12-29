<?php

namespace Docker\API\Resource;

use Joli\Jane\Swagger\Client\QueryParam;
use Joli\Jane\Swagger\Client\Resource;

class NetworkResource extends Resource
{
    /**
     * List networks.
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
        $url      = sprintf('/v1.21/networks?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Network[]', 'json');
        }

        return $response;
    }

    /**
     * Remove a network.
     * 
     * @param mixed  $id         Network id or name
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function remove($id, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/networks/%s?%s', $id, $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('DELETE', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Inspect network.
     * 
     * @param mixed  $id         Network id or name
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function inspect($id, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/networks/%s?%s', $id, $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Network', 'json');
        }

        return $response;
    }

    /**
     * Create network.
     * 
     * @param mixed  $networkConfig Network configuration
     * @param array  $parameters    List of parameters
     * @param string $fetch         Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create($networkConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/networks/create?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $networkConfig);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('201' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\NetworkCreateResult', 'json');
        }

        return $response;
    }

    /**
     * Connect a container to a network.
     * 
     * @param mixed  $container  Container
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function connect($container, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/networks/{id}/connect?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $container);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Disconnect a container to a network.
     * 
     * @param mixed  $container  Container
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function disconnect($container, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/networks/{id}/disconnect?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $container);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }
}
