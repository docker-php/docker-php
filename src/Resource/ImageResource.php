<?php

namespace Docker\Resource;

use Docker\Stream\BuildStream;
use Docker\Stream\CreateImageStream;
use Docker\Stream\PushStream;
use Docker\Stream\TarStream;
use Joli\Jane\OpenApi\Runtime\Client\QueryParam;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

class ImageResource extends OverrideResource
{
    public function imageBuild($inputStream, $parameters = [], $fetch = Resource::FETCH_OBJECT)
    {
        if (is_resource($inputStream)) {
            $inputStream = new TarStream($inputStream);
        }

        $response = $this->resource->imageBuild($inputStream, $parameters, Resource::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new BuildStream($response->getBody(), $this->serializer, $this->version);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $buildInfoList = [];

                $stream = new BuildStream($response->getBody(), $this->serializer, $this->version);
                $stream->onFrame(function ($buildInfo) use (&$buildInfoList) {
                    $buildInfoList[] = $buildInfo;
                });
                $stream->wait();

                return $buildInfoList;
            }
        }

        return $response;
    }

    public function imageCreate($inputStream = null, $parameters = [], $fetch = Resource::FETCH_OBJECT)
    {
        if (isset($parameters['X-Registry-Auth']) && is_object($parameters['X-Registry-Auth'])) {
            $parameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($parameters['X-Registry-Auth'], 'json'));
        }

        $response = $this->resource->imageCreate($inputStream, $parameters, Resource::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new CreateImageStream($response->getBody(), $this->serializer, $this->version);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $createImageInfoList = [];

                $stream = new CreateImageStream($response->getBody(), $this->serializer, $this->version);
                $stream->onFrame(function ($createImageInfo) use (&$createImageInfoList) {
                    $createImageInfoList[] = $createImageInfo;
                });
                $stream->wait();

                return $createImageInfoList;
            }
        }

        return $response;
    }

    public function imagePush($name, $parameters = [], $fetch = Resource::FETCH_OBJECT)
    {
        if (isset($parameters['X-Registry-Auth']) && is_object($parameters['X-Registry-Auth'])) {
            $parameters['X-Registry-Auth'] = base64_encode($this->serializer->serialize($parameters['X-Registry-Auth'], 'json'));
        }

        $queryParam = new QueryParam();
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('X-Registry-Auth', null);
        $queryParam->setHeaderParameters(['X-Registry-Auth']);

        $url      = 'http://localhost/images/{name}/push';
        $url      = str_replace('{name}', $name, $url);
        $url      = $url . ('?' . $queryParam->buildQueryString($parameters));

        $headers  = array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));

        $body     = $queryParam->buildFormDataString($parameters);

        $request  = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new PushStream($response->getBody(), $this->serializer, $this->version);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $pushImageInfoList = [];

                $stream = new PushStream($response->getBody(), $this->serializer, $this->version);
                $stream->onFrame(function ($pushImageInfo) use (&$pushImageInfoList) {
                    $pushImageInfoList[] = $pushImageInfo;
                });
                $stream->wait();

                return $pushImageInfoList;
            }
        }

        return $response;
    }
}