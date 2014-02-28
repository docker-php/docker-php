<?php

namespace Docker;

use Docker\Context\Context;
use Docker\Http\Client as HttpClient;
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
     * @param Docker\Http\Client    $client
     * @param array                 $array
     */
    public function __construct(HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new HttpClient('tcp://127.0.0.1:4243');
    }

    /**
     * @return Docker\Http\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return Docker\Manager\ContainerManager
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->httpClient);
        }

        return $this->containerManager;
    }

    /**
     * @return Docker\Manager\ImageManager
     */
    public function getImageManager()
    {
        if (null === $this->imageManager) {
            $this->imageManager = new ImageManager($this->httpClient);
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
     * @param boolean                            $wait     Wait for build to finish before returning response (default to true)
     *
     * @return Docker\Http\Response
     */
    public function build(ContextInterface $context, $name, $quiet = false, $cache = true, $rm = false, $wait = true)
    {
        $request = $this->httpClient->post(['/build{?data*}', ['data' => [
            'q' => (integer) $quiet,
            't' => $name,
            'nocache' => (integer) !$cache,
            'rm' => (integer) $rm
        ]]], [
            'Content-Type' => 'application/tar'
        ]);

        $request->setProtocolVersion('1.1');
        $request->setContent($context->read(), 'application/tar');

        $response = $this->httpClient->send($request, $wait);

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

        $request = $this->httpClient->post(['/commit{?config*}', ['config' => $config]]);
        $response = $this->httpClient->send($request);

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), (string) $response->getContent());
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
