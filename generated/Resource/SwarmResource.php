<?php

namespace Docker\API\Resource;

use Joli\Jane\OpenApi\Client\QueryParam;
use Joli\Jane\OpenApi\Client\Resource;

class SwarmResource extends Resource
{
    /**
     * Initialize a new Swarm.
     *
     * @param \Docker\API\Model\SwarmConfig $swarmConfig Config for the Swarm
     * @param array                         $parameters  List of parameters
     * @param string                        $fetch       Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function initialize(\Docker\API\Model\SwarmConfig $swarmConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/swarm/init';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $this->serializer->serialize($swarmConfig, 'json');
        $request    = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Join an existing new Swarm.
     *
     * @param \Docker\API\Model\SwarmJoinConfig $swarmJoinConfig Config for joining the Swarm
     * @param array                             $parameters      List of parameters
     * @param string                            $fetch           Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function join(\Docker\API\Model\SwarmJoinConfig $swarmJoinConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/swarm/join';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $this->serializer->serialize($swarmJoinConfig, 'json');
        $request    = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Leave a Swarm.
     *
     * @param array  $parameters List of parameters
     * @param string $fetch      Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function leave($parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $url        = '/swarm/leave';
        $url        = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers    = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body       = $queryParam->buildFormDataString($parameters);
        $request    = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response   = $this->httpClient->sendRequest($request);

        return $response;
    }

    /**
     * Update a Swarm.
     *
     * @param \Docker\API\Model\SwarmUpdateConfig $swarmUpdateConfig Config for updating the Swarm
     * @param array                               $parameters        {
     *
     *     @var int $version The version number of the swarm object being updated. This is required to avoid conflicting writes.
     *     @var bool $rotateWorkerToken Set to true (or 1) to rotate the worker join token.
     *     @var bool $rotateManagerToken Set to true (or 1) to rotate the manager join token.
     * }
     *
     * @param string $fetch Fetch mode (object or response)
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(\Docker\API\Model\SwarmUpdateConfig $swarmUpdateConfig, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        $queryParam = new QueryParam();
        $queryParam->setRequired('version');
        $queryParam->setDefault('rotateWorkerToken', null);
        $queryParam->setDefault('rotateManagerToken', null);
        $url      = '/swarm/update';
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));
        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));
        $body     = $this->serializer->serialize($swarmUpdateConfig, 'json');
        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        return $response;
    }
}
