<?php

namespace Docker\Manager;

use Guzzle\Http\Client;

/**
 * Docker\ImageManager
 */
class ImageManager
{
    /**
     * @var Zend\Http\Client
     */
    private $client;

    /**
     * @param Zend\Http\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}