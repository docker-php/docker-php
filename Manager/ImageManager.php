<?php

namespace Docker\Manager;

use Guzzle\Http\Client;

class ImageManager
{
    /**
     * @var Guzzle\Http\Client
     */
    private $client;

    /**
     * @var array
     */
    private $images = array();

    /**
     * @param Guzzle\Http\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param Docker\Image
     * 
     * @return Docker\Manager\ImageManager
     */
    public function addImage(Image $image)
    {
        if (!array_key_exists($image->getId(), $this->images)) {
            $this->images[$image->getId()] = $image;
        }

        return $this;
    }
}