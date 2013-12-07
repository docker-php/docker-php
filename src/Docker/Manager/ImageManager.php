<?php

namespace Docker\Manager;

use Guzzle\Http\Client;

/**
 * Docker\ImageManager
 */
class ImageManager
{
    /**
     * @var Guzzle\Http\Client
     */
    private $client;

    /**
     * @param Guzzle\Http\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}