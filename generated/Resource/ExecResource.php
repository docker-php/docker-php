<?php

namespace Docker\API\Resource;

use Joli\Jane\Swagger\Client\QueryParam;
use Joli\Jane\Swagger\Client\Resource;

class ExecResource extends Resource
{
    /**
     * Sets up an exec instance in a running container id.
     * 
     * @param mixed  $execConfig Exec configuration
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create($execConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/containers/{id}/exec?%s', $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $execConfig);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('201' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ExecCreateResult', 'json');
        }

        return $response;
    }

    /**
     * Starts a previously set up exec instance id. If detach is true, this API returns after starting the exec command. Otherwise, this API sets up an interactive session with the exec command.
     * 
     * @param mixed  $execStartConfig Exec configuration
     * @param mixed  $id              Exec instance id
     * @param array  $parameters      List of parameters
     * @param string $fetch           Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function start($execStartConfig, $id, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/exec/%s/start?%s', $id, $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), $execStartConfig);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Resize the tty session used by the exec command id.
     * 
     * @param mixed  $id         Exec instance id
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function resize($id, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setDefault('w', null);
        $url      = sprintf('/v1.21/exec/%s/resize?%s', $id, $queryParam->buildQueryString($parameters));
        $request  = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), null);
        $request  = $request->withHeader('Host', 'localhost');
        $response = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }

        return $response;
    }

    /**
     * Return low-level information about the exec command id.
     * 
     * @param mixed  $id         Exec instance id
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function inspect($id, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = sprintf('/v1.21/exec/%s/json?%s', $id, $queryParam->buildQueryString($parameters));
        $request    = $this->messageFactory->createRequest('POST', $url, $queryParam->buildHeaders($parameters), null);
        $request    = $request->withHeader('Host', 'localhost');
        $response   = $this->httpClient->sendRequest($request);
        if (self::FETCH_RESPONSE == $fetch) {
            return $response;
        }
        if ('200' == $response->getStatusCode()) {
            return $this->serializer->deserialize($response->getBody()->getContents(), 'Docker\\API\\Model\\ExecCommand', 'json');
        }

        return $response;
    }
}
