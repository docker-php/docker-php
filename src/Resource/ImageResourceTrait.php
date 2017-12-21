<?php

declare(strict_types=1);

namespace Docker\Resource;

use Docker\API\Resource\ImageResourceTrait as BaseImageResourceTrait;
use Docker\Stream\BuildStream;
use Docker\Stream\CreateImageStream;
use Docker\Stream\PushStream;
use Docker\Stream\TarStream;
use Joli\Jane\OpenApi\Runtime\Client\QueryParam;
use Joli\Jane\OpenApi\Runtime\Client\Resource;

trait ImageResourceTrait
{
    use BaseImageResourceTrait {
        imageBuild as imageBuildLegacy;
        imageCreate as imageCreateLegacy;
    }

    public function imageBuild($inputStream, array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        if (\is_resource($inputStream)) {
            $inputStream = new TarStream($inputStream);
        }

        $response = $this->imageBuildLegacy($inputStream, $parameters, Resource::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new BuildStream($response->getBody(), $this->serializer);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $buildInfoList = [];

                $stream = new BuildStream($response->getBody(), $this->serializer);
                $stream->onFrame(function ($buildInfo) use (&$buildInfoList): void {
                    $buildInfoList[] = $buildInfo;
                });
                $stream->wait();

                return $buildInfoList;
            }
        }

        return $response;
    }

    public function imageCreate($inputImage, array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        if (isset($parameters['X-Registry-Auth']) && \is_object($parameters['X-Registry-Auth'])) {
            $parameters['X-Registry-Auth'] = \base64_encode($this->serializer->serialize($parameters['X-Registry-Auth'], 'json'));
        }

        $response = $this->imageCreateLegacy($inputImage, $parameters, Resource::FETCH_RESPONSE);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new CreateImageStream($response->getBody(), $this->serializer);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $createImageInfoList = [];

                $stream = new CreateImageStream($response->getBody(), $this->serializer);
                $stream->onFrame(function ($createImageInfo) use (&$createImageInfoList): void {
                    $createImageInfoList[] = $createImageInfo;
                });
                $stream->wait();

                return $createImageInfoList;
            }
        }

        return $response;
    }

    public function imagePush(string $name, array $parameters = [], string $fetch = self::FETCH_OBJECT)
    {
        if (isset($parameters['X-Registry-Auth']) && \is_object($parameters['X-Registry-Auth'])) {
            $parameters['X-Registry-Auth'] = \base64_encode($this->serializer->serialize($parameters['X-Registry-Auth'], 'json'));
        }

        $queryParam = new QueryParam();
        $queryParam->setDefault('tag', null);
        $queryParam->setDefault('X-Registry-Auth', null);
        $queryParam->setHeaderParameters(['X-Registry-Auth']);

        $url = '/images/{name}/push';
        $url = \str_replace('{name}', \urlencode($name), $url);
        $url = $url.('?'.$queryParam->buildQueryString($parameters));

        $headers = \array_merge(['Host' => 'localhost'], $queryParam->buildHeaders($parameters));

        $body = $queryParam->buildFormDataString($parameters);

        $request = $this->messageFactory->createRequest('POST', $url, $headers, $body);
        $response = $this->httpClient->sendRequest($request);

        if (200 === $response->getStatusCode()) {
            if (self::FETCH_STREAM === $fetch) {
                return new PushStream($response->getBody(), $this->serializer);
            }

            if (Resource::FETCH_OBJECT === $fetch) {
                $pushImageInfoList = [];

                $stream = new PushStream($response->getBody(), $this->serializer);
                $stream->onFrame(function ($pushImageInfo) use (&$pushImageInfoList): void {
                    $pushImageInfoList[] = $pushImageInfo;
                });
                $stream->wait();

                return $pushImageInfoList;
            }
        }

        return $response;
    }
}
