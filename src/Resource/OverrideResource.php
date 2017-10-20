<?php

namespace Docker\Resource;

use Http\Client\Common\FlexibleHttpClient;

abstract class OverrideResource
{
    const FETCH_STREAM = 'stream';

    protected $resource;

    protected $httpClient;

    protected $messageFactory;

    protected $serializer;

    protected $version;

    public function __construct($httpClient, $messageFactory, $serializer, $resourceClass, $version)
    {
        $this->resource = new $resourceClass($httpClient, $messageFactory, $serializer);
        $this->httpClient = new FlexibleHttpClient($httpClient);
        $this->messageFactory = $messageFactory;
        $this->serializer = $serializer;
        $this->version = $version;
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->resource, $name], $arguments);
    }
}