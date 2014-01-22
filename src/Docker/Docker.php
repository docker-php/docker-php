<?php

namespace Docker;

use Docker\Context\Context;
use Docker\Http\Client;
use Docker\Manager\ContainerManager;
use Docker\Manager\ImageManager;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Context\ContextInterface;

/**
 * Docker\Docker
 */
class Docker
{
    const BUILD_VERBOSE = false;
    const BUILD_QUIET = true;

    const BUILD_CACHE = true;
    const BUILD_NO_CACHE = false;

    /**
     * @var Docker\Http\Client
     */
    private $client;

    /**
     * @var array
     */
    private $containerManager;

    /**
     * @var array
     */
    private $imageManager;

    /**
     * @param Docker\Http\Client $client
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client('tcp://127.0.0.1:4243');
    }

    /**
     * @return Docker\Manager\ContainerManager
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->client);
        }

        return $this->containerManager;
    }

    /**
     * @return Docker\Manager\ImageManager
     */
    public function getImageManager()
    {
        if (null === $this->imageManager) {
            $this->imageManager = new ImageManager($this->client);
        }

        return $this->imageManager;
    }

    /**
     * Build an image with docker
     *
     * @param Docker\Context\ContextInterface    $context
     * @param string                             $name
     * @param boolean                            $quiet
     * @param boolean                            $rm       Remove intermediate container during build
     *
     * @return Docker\Http\Response
     */
    public function build(ContextInterface $context, $name, $quiet = false, $cache = true, $rm = false)
    {
        $request = $this->client->post(['/build{?data*}', ['data' => [
            'q' => (integer) $quiet,
            't' => $name,
            'nocache' => (integer) !$cache,
            'rm' => (integer) $rm
        ]]], [
            'Content-Type' => 'application/tar'
        ]);

        # http client does not support chunked responses yet
        $request->setProtocolVersion('1.1');
        $request->setContent($context->toStream(), 'application/tar');

        $response = $this->client->send($request);

        return $response;
    }

    /**
     * @param Docker\Container $container
     * @param array $config
     *
     * @return Docker\Image
     *
     * @see http://docs.docker.io/en/latest/api/docker_remote_api_v1.7/#create-a-new-image-from-a-container-s-changes
     */
    public function commit(Container $container, $config = array())
    {
        if (isset($config['run'])) {
            $config['run'] = json_encode($config['run']);
        }

        $config['container'] = $container->getId();

        $request = $this->client->post(['/commit{?config*}', ['config' => $config]]);
        $response = $this->client->send($request);

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $image = new Image();
        $image->setId($response->json(true)['Id']);

        if (array_key_exists('repo', $config)) {
            $image->setRepository($config['repo']);
        }

        if (array_key_exists('tag', $config)) {
            $image->setTag($config['tag']);
        }

        return $image;
    }
}
