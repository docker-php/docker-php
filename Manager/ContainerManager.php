<?php

namespace Docker\Manager;

use Docker\Container;

use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Exception\ServerErrorException;
use Docker\Exception\ContainerNotFoundException;

use Guzzle\Http\Client;

/**
 * Docker\Manager\ContainerManager
 */
class ContainerManager
{
    /**
     * @var Guzzle\Http\Client
     */
    private $client;

    /**
     * @param Guzzle\Http\Client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param string    $image
     * @param array     $cmd
     * 
     * @return Docker\Manager\ContainerManager
     */
    public function create(Container $container)
    {
        $request = $this->client->post('/containers/create', null, json_encode($container->getConfig()));
        $response = $request->send();

        if ($response->getStatusCode() === 500) {
            throw new ServerErrorException();
        }

        if ($response->getStatusCode() !== 201) {
            throw new Exception();
        }

        $container->setId($response->json()['Id']);

        return $this;
    }

    /**
     * @param Docker\Container $container
     * 
     * @return Docker\Manager\ContainerManager
     */
    public function start(Container $container)
    {
        $request = $this->client->post(['/containers/{id}/start', ['id' => $container->getId()]]);
        $response = $request->send();

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId());
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        return $this;
    }

    /**
     * @param Docker\Container $container
     * 
     * @return Docker\Manager\ContainerManager
     */
    public function run(Container $container)
    {
        return $this
            ->create($container)
            ->start($container);
    }

    /**
     * @param Docker\Container $container
     * 
     * @return Docker\Manager\ContainerManager
     */
    public function wait(Container $container)
    {
        $request = $this->client->post(['/containers/{id}/wait', ['id' => $container->getId()]]);
        $response = $request->send();

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId());
        }

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $container->setExitCode($response->json()['StatusCode']);

        return $this;
    }
}