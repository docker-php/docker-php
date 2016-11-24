<?php

namespace Docker\Manager;

use Docker\API\Resource\ServiceResource;
use Docker\API\Model\AuthConfig;
use Docker\Stream\CreateImageStream;
use Docker\API\Model\CreateImageInfo;

class ServiceManager extends ServiceResource
{
    /**
     * {@inheritdoc}
     *
     * @return \Psr\Http\Message\ResponseInterface|CreateImageInfo[]|CreateImageStream
     */
    public function create(\Docker\API\Model\ServiceSpec $serviceSpec, $parameters = [], $fetch = self::FETCH_OBJECT)
    {
        if (isset($parameters['X-Registry-Auth']) && $parameters['X-Registry-Auth'] instanceof AuthConfig) {
            $parameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($parameters['X-Registry-Auth'], 'json'));
        }

        $response = parent::create($serviceSpec, $parameters, self::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new CreateImageStream($response->getBody(), $this->serializer);
            }

            if (self::FETCH_OBJECT === $fetch) {
                $createImageInfoList = [];

                $stream = new CreateImageStream($response->getBody(), $this->serializer);
                $stream->onFrame(function (CreateImageInfo $createImageInfo) use (&$createImageInfoList) {
                    $createImageInfoList[] = $createImageInfo;
                });
                $stream->wait();

                return $createImageInfoList;
            }
        }

        return $response;
    }
}

