<?php

namespace Docker;

use Docker\Manager\ContainerManager;
use Docker\Exception\UnexpectedStatusCodeException;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Stdlib\Parameters;
use Zend\Http\Headers;
use Zend\Http\Client\Adapter\StreamInterface;
use Zend\Http\Client\Exception\RuntimeException;
use Docker\Exception\ContainerNotFoundException;

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
     * @var Zend\Http\Client
     */
    private $client;

    /**
     * @var string
     */
    private $uri;

    /**
     * @var array
     */
    private $containerManager;

    /**
     * @var array
     */
    private $imageManager;

    /**
     * @param Guzzle\Http\Client                    $client
     * @param Guzzle\Stream\PhpStreamRequestFactory $factory
     */
    public function __construct(Client $client = null)
    {
        $this->client = $client ?: new Client('http://127.0.0.1:4243');

        if (!$this->client->getAdapter() instanceof StreamInterface) {
            throw new \RuntimeException("HTTP Client does not support output streaming");
        }

        $this->uri = sprintf("%s://%s:%s", $this->client->getUri()->getScheme(), $this->client->getUri()->getHost(), $this->client->getUri()->getPort());
    }

    /**
     * @return Docker\Docker
     */
    public function enableDebug()
    {
        $this->client->addSubscriber(LogPlugin::getDebugPlugin());

        return $this;
    }

    /**
     * @return Docker\Manager\ContainerManager
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->client, $this->uri);
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
     * @param Docker\Context    $context
     * @param string            $name
     * @param boolean           $quiet
     *
     * @return Guzzle\Stream\StreamInterface
     *
     * The `q` argument seems to be ignored right now (same behavior observed in the CLI client)
     */
    public function build(Context $context, $name, $quiet = false, $cache = true)
    {
        $params = new Parameters();
        $params->set('q', $quiet);
        $params->set('t', $name);
        $params->set('nocache', !$cache);

        $headers = new Headers();
        $headers->addHeaderLine('content-type', 'application/tar');

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri(sprintf("%s/build", $this->uri));
        $request->setContent($context->toStream());
        $request->setHeaders($headers);
        $request->setQuery($params);

        return $this->client->send($request);
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

        $params = new Parameters();
        $params->fromArray($config);

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri(sprintf("%s/commit", $this->uri));
        $request->setQuery($params);

        $response = $this->client->send($request);

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId(), null, $response->getBody());
        }

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), $response->getContent());
        }

        $image = new Image();
        $image->setId(json_decode($response->getBody(), true)['Id']);

        if (array_key_exists('repo', $config)) {
            $image->setRepository($config['repo']);
        }

        if (array_key_exists('tag', $config)) {
            $image->setTag($config['tag']);
        }

        return $image;
    }
}