<?php

namespace Docker\API\Resource;

use Joli\Jane\Swagger\Client\QueryParam;
use Joli\Jane\Swagger\Client\Resource;

class MiscResource extends Resource
{
    /**
     * Check auth configuration.
     * 
     * @param mixed  $authConfig Authentication to check
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function checkAuthentication($authConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/auth?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $authConfig);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Display system-wide information.
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getSystemInformation($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/info?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\SystemInformation', 'json');
        }

        return $response;
    }

    /**
     * Show the docker version information.
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getVersion($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/version?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\Version', 'json');
        }

        return $response;
    }

    /**
     * Ping the docker server.
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function ping($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/_ping?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Get container events from docker, either in real time via streaming, or via polling (using since).
     * 
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getEvents($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('since', null);
        $queryParam->setDefault('until', null);
        $queryParam->setDefault('filters', null);
        $url      = sprintf('/v1.21/events?%s', $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('GET', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }
}
