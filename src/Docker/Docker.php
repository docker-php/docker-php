<?php

namespace Docker;

use Docker\Context\Context;
use Docker\Http\DockerClient;
use Docker\Http\Stream\StreamCallbackInterface;
use Docker\Manager\ContainerManager;
use Docker\Manager\ImageManager;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Context\ContextInterface;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Stream\Stream;

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
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var array
     */
    private $containerManager;

    /**
     * @var array
     */
    private $imageManager;

    /**
     * @param HttpClient $httpClient Http client to use with Docker
     */
    public function __construct(HttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?: new DockerClient();
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @return \Docker\Manager\ContainerManager
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->httpClient);
        }

        return $this->containerManager;
    }

    /**
     * @return \Docker\Manager\ImageManager
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
     * @param \Docker\Context\ContextInterface   $context  Context to build
     * @param string                             $name     Name of the wanted image
     * @param callable                           $callback A callback to be called for having log of build
     * @param boolean                            $quiet    Quiet build (doest not output commands during build)
     * @param boolean                            $cache    Use docker cache
     * @param boolean                            $rm       Remove intermediate container during build
     */
    public function build(ContextInterface $context, $name, callable $callback = null, $quiet = false, $cache = true, $rm = false)
    {
        $content  = is_resource($context->read()) ? new Stream($context->read()) : $context->read();
        $response = $this->httpClient->post(['/build{?data*}', ['data' => [
            'q' => (integer) $quiet,
            't' => $name,
            'nocache' => (integer) !$cache,
            'rm' => (integer) $rm
        ]]], [
            'headers' => array('Content-Type' => 'application/tar'),
            'body'    => $content,
            'stream'  => true
        ]);

        if (null === $callback) {
            $callback = function() {};
        }

        $stream = $response->getBody();

        if ($stream instanceof StreamCallbackInterface) {
            $stream->readWithCallback($callback);
        } else {
            $callback($stream->__toString(), null);
        }
    }

    /**
     * Commit a container into an image
     *
     * @param \Docker\Container $container
     * @param array $config
     *
     * @throws Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Image
     *
     * @see http://docs.docker.io/en/latest/api/docker_remote_api_v1.7/#create-a-new-image-from-a-container-s-changes
     */
    public function commit(Container $container, $config = array())
    {
        if (isset($config['run'])) {
            $config['run'] = json_encode($config['run']);
        }

        $config['container'] = $container->getId();

        $response = $this->httpClient->post(['/commit{?config*}', ['config' => $config]]);

        if ($response->getStatusCode() !== "201") {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), (string) $response->getBody());
        }

        $image = new Image();
        $image->setId($response->json()['Id']);

        if (array_key_exists('repo', $config)) {
            $image->setRepository($config['repo']);
        }

        if (array_key_exists('tag', $config)) {
            $image->setTag($config['tag']);
        }

        return $image;
    }
}
