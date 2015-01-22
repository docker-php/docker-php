<?php

namespace Docker\Manager;

use Docker\Exception\ImageNotFoundException;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Http\Stream\StreamCallbackInterface;
use Docker\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

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
     * Get all images from docker daemon
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
            $stream->readWithCallback(function ($output) use (&$imageString) {
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
     * @param string $repository Name of image to get
     * @param string $tag        Tag of the image to get (default "latest")
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
     * Inspect an image
     *
     * @param \Docker\Image $image
     *
     * @throws \Docker\Exception\ImageNotFoundException
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     * @throws \GuzzleHttp\Exception\RequestException
     *
     * @return @return json data from docker inspect
     */
    public function inspect(Image $image)
    {
        try {
            # Images need not have a name and tag,(__toString() may return ':')
            # so prefer an id hash as the key
            if (null != $image->getId()) {
              $id = $image->getId();
            } else {
              $id = $image->__toString();
            }
            $response = $this->client->get(['/images/{id}/json', ['id' => $id]]);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == "404") {
                throw new ImageNotFoundException($id, $e);
            }

            throw $e;
        }

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $data = $response->json();

        $image->setId($data['Id']);
        // @TODO Add extra info on image

        return $data;
    }

    /**
     * Pull an image from registry
     *
     * @param string   $name     Name of image to pull
     * @param string   $tag      Tag of image
     * @param callable $callback Callback to retrieve log of pull
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return Image
     */
    public function pull($name, $tag = 'latest', callable $callback = null)
    {
        if (null === $callback) {
            $callback = function () {};
        }

        $response = $this->client->post(['/images/create?fromImage={image}&tag={tag}', ['image' => $name, 'tag' => $tag]], [
            'callback' => $callback,
            'wait'     => true,
        ]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
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
            'noprune' => $noprune,
            'wait'    => true
        ]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $this;
    }

    /**
     * Search for an image on Docker Hub.
     *
     * @param string $term term to search
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return array
     */
    public function search($term)
    {
        $response = $this->client->get(
            [
                '/images/search?term={term}',
                [
                    'term' => $term,
                ]
            ]
        );

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response->json();
    }
}
