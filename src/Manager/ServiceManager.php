<?php

namespace Docker\Manager;

use Docker\API\Resource\ServiceResource;
use Docker\API\Model\AuthConfig;
use Docker\API\Model\ServiceSpec;

class ServiceManager extends ServiceResource
{
    /**
     * {@inheritdoc}
     *
     * @return \Psr\Http\Message\ResponseInterface|\Docker\API\Model\ServiceCreateResponse
     */
    public function create(ServiceSpec $serviceSpec, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        if (isset($parameters['X-Registry-Auth']) && $parameters['X-Registry-Auth'] instanceof AuthConfig) {
            $parameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($parameters['X-Registry-Auth'], 'json'));
        }

        return parent::create($serviceSpec, $parameters, $fetch);
    }


}
