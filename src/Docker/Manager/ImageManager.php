<?php

namespace Docker\Manager;

use GuzzleHttp\Client;

/**
 * Docker\ImageManager
 */
class ImageManager
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}