<?php

namespace Docker\Manager;

use Docker\Exception\ImageNotFoundException;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Image;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Message\Response;

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
        /** @var Response $response */
        $response = $this->client->get('/images/json');

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $images = $response->json();

        if (!is_array($images)) {
            return [];
        }

        $coll = [];

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

        $data = $this->inspect($image);
        $image->setId($data['Id']);

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
     * @return array json data from docker inspect
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

        return $response->json();
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
        $data = $this->inspect($image);

        if (!$image->getId()) {
            $image->setId($data['Id']);
        }

        return $image;
    }

    /**
     * Remove an image from docker daemon
     *
     * @param Image   $image   Image to remove
     * @param boolean $force   Force removal of image (default false)
     * @param boolean $noprune Do not remove parent images (default false)
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return ImageManager
     */
    public function remove(Image $image, $force = false, $noprune = false)
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
     * Remove multiple images from docker daemon
     *
     * @param Image[]|array $images  Images to remove
     * @param boolean       $force   Force removal of image (default false)
     * @param boolean       $noprune Do not remove parent images (default false)
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return ImageManager
     */
    public function removeImages(array $images, $force = false, $noprune = false)
    {
        foreach ($images as $image) {
            if (!$image instanceof Image) {
                $imageId = $image;

                $image = new Image();
                $image->setId($imageId);
            }

            $this->remove($image, $force, $noprune);
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

    /**
     * Tag an image
     *
     * @param Image $image image to tag
     * @param $repository Repository name to use
     * @param string $tag Tag to use
     * @param bool $force Force to set tag even if an image with the same name already exists ?
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return ImageManager
     */
    public function tag(Image $image, $repository, $tag = 'latest', $force = false)
    {
        $response = $this->client->post([
            '/images/{name}/tag?repo={repository}&tag={tag}&force={force}', [
                'name' => $image->getId(),
                'repository' => $repository,
                'tag' => $tag,
                'force' => intval($force)
            ]
        ]);

        if ($response->getStatusCode() !== "201") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $image->setRepository($repository);
        $image->setTag($tag);

        return $this;
    }

    /**
     * Get history of an image
     *
     * @param Image $image
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return array
     */
    public function history(Image $image)
    {
        $response = $this->client->get(['/images/{name}/history', [
            'name' => $image->__toString()
        ]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response->json();
    }
}
