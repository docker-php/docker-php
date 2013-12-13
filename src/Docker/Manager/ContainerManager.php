<?php

namespace Docker\Manager;

use Docker\Container;
use Docker\Json;

use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Exception\ServerErrorException;
use Docker\Exception\ContainerNotFoundException;

use Zend\Http\Client;
use Zend\Http\Request;
use Zend\Http\Headers;

/**
 * Docker\Manager\ContainerManager
 */
class ContainerManager
{
    /**
     * @var Zend\Http\Client
     */
    private $client;

    /**
     * @var string
     */
    private $uri;

    /**
     * @param Zend\Http\Client $client Client used to call docker API
     * @param string           $uri    Uri of docker API as the uri change in client from request to request, we need to keep track of the "root" uri
     */
    public function __construct(Client $client, $uri = null)
    {
        $this->client = $client;
        $this->uri = $uri ? $uri : $uri;
    }

    /**
     * @param string $id
     *
     * @return Docker\Container|null
     */
    public function find($id)
    {
        $container = new Container();
        $container->setId($id);

        try {
            $this->inspect($container);
        } catch (ContainerNotFoundException $e) {
            return null;
        }

        return $container;
    }

    /**
     * @param Docker\Container $container
     *
     * @return Docker\ContainerManager
     */
    public function inspect(Container $container)
    {
        $request = new Request();
        $request->setUri(sprintf('%s/containers/%s/json', $this->uri, $container->getId()));
        $request->setMethod(Request::METHOD_GET);

        $response = $this->client->send($request);

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId(), null, $response->getBody());
        }

        $container->setRuntimeInformations(json_decode($response->getBody(), true));

        return $this;
    }

    /**
     * @param Docker\Container $container
     *
     * @return Docker\Manager\ContainerManager
     */
    public function create(Container $container)
    {
        $headers = new Headers();
        $headers->addHeaderLine('content-type', 'application/json');

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setContent(Json::encode($container->getConfig()));
        $request->setHeaders($headers);
        $request->setUri($this->uri.'/containers/create');

        $response = $this->client->send($request);

        if ($response->getStatusCode() === 500) {
            throw new ServerErrorException($response->getBody());
        }

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), $response->getBody());
        }

        $container->setId(json_decode($response->getBody(), true)['Id']);

        return $this;
    }

    /**
     * @param Docker\Container $container
     *
     * @return Docker\Manager\ContainerManager
     */
    public function start(Container $container, array $hostConfig = array())
    {
        $headers = new Headers();
        $headers->addHeaderLine('content-type', 'application/json');

        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setContent(Json::encode($hostConfig));
        $request->setHeaders($headers);
        $request->setUri(sprintf('%s/containers/%s/start', $this->uri, $container->getId()));

        $response = $this->client->send($request);

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId(), null, $response->getBody());
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), $response->getBody());
        }

        $this->inspect($container);

        return $this;
    }

    /**
     * @param Docker\Container  $container
     * @param array             $hostConfig
     *
     * @return Docker\Manager\ContainerManager
     */
    public function run(Container $container, array $hostConfig = array())
    {
        $this
            ->create($container)
            ->start($container, $hostConfig);

        return $this;
    }

    /**
     * @param Docker\Container $container
     *
     * @return Docker\Manager\ContainerManager
     */
    public function wait(Container $container)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri(sprintf('%s/containers/%s/wait', $this->uri, $container->getId()));

        $response = $this->client->send($request);

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId(), null, $response->getBody());
        }

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $container->setExitCode(json_decode($response->getBody(), true)['StatusCode']);

        $this->inspect($container);

        return $this;
    }

    /**
     * @param Docker\Container $container
     * @param integer          $timeout
     *
     * @return Docker\Manager\ContainerManager
     */
    public function stop(Container $container, $timeout = 5)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);
        $request->setUri(sprintf('%s/containers/%s/stop?t=%s', $this->uri, $container->getId(), $timeout));

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId(), null, $response->getBody());
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), $response->getBody());
        }

        $this->inspect($container);

        return $this;
    }

    /**
     * @param Docker\Container  $container
     * @param boolean           $volumes
     *
     * @return Docker\Manager\ContainerManager
     */
    public function remove(Container $container, $volumes = false)
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_DELETE);
        $request->setUri(sprintf('%s/containers/%s?v=%s', $this->uri, $container->getId(), $volumes));

        $response = $this->client->send($response);

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId(), null, $response->getBody());
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode(), $response->getBody());
        }

        return $this;
    }
}