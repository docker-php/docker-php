<?php

namespace Docker\Manager;

use Docker\Exception\ImageNotFoundException;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Http\Stream\StreamCallbackInterface;
use Docker\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

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

    /**
     * Get all images in docker daemon
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return Image[]
     */
    public function findAll()
    {
        $response = $this->client->get('/images/json');

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $coll   = [];
        $stream = $response->getBody();
        $imageString = "";

        if ($stream instanceof StreamCallbackInterface) {
            $stream->readWithCallback(function ($output) use(&$imageString) {
                $imageString .= $output;
            });
        } else {
            $imageString = $response->getBody()->__toString();
        }

        $images = json_decode($imageString, true);

        if (!is_array($images)) {
            return [];
        }

        foreach ($images as $data) {
            $image = new Image();
            $image->setId($data['Id']);

            foreach ($data['RepoTags'] as $repoTag) {
                list($repository, $tag) = explode(':', $repoTag);

                $tagImage = clone $image;
                $tagImage->setRepository($repository);
                $tagImage->setTag($tag);

                $coll[] = $tagImage;
            }
        }

        return $coll;
    }

    /**
     * Get an image from docker daemon
     *
     * @param  string $repository Name of image to get
     * @param  string $tag        Tag of the image to get (default to latest)
     *
     * @return Image
     */
    public function find($repository, $tag = 'latest')
    {
        $image = new Image($repository, $tag);

        $this->inspect($image);

        return $image;
    }

    /**
     * Inspect a image
     *
     * @param \Docker\Image $image
     *
     * @throws \Docker\Exception\ImageNotFoundException
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     * @throws \GuzzleHttp\Exception\ClientException
     *
     * @return ImageManager
     */
    public function inspect(Image $image)
    {
        try {
            $response = $this->client->get(['/images/{id}/json', ['id' => $image->__toString()]]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == "404") {
                throw new ImageNotFoundException($image->__toString(), $e);
            }

            throw $e;
        }

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $data = $response->json();

        $image->setId($data['Id']);
        // @TODO Add extra info on image

        return $this;
    }

    /**
     * Pull an image from registry
     *
     * @param  string $name Name of image to pull
     * @param  string $tag  Tag of image
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return Image
     */
    public function pull($name, $tag = 'latest')
    {
        $response = $this->client->post(['/images/create?fromImage={image}&tag={tag}', ['image' => $name, 'tag' => $tag]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $stream = $response->getBody();

        if ($stream instanceof StreamCallbackInterface) {
            $stream->readWithCallback(function () {});
        } else {
            $stream->__toString();
        }

        $image = new Image($name, $tag);

        $this->inspect($image);

        return $image;
    }

    /**
     * Delete an image from docker daemon
     *
     * @param Image   $image   Image to delete
     * @param boolean $force   Force deletion of image (default false)
     * @param boolean $noprune Do not delete parent images (default false)
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return ImageManager
     */
    public function delete(Image $image, $force = false, $noprune = false)
    {
        $response = $this->client->delete(['/images/{image}?force={force}&noprune={noprune}', [
            'image'   => $image->__toString(),
            'force'   => $force,
            'noprune' => $noprune
        ]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $this;
    }
}