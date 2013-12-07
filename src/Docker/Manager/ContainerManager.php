<?php

namespace Docker\Manager;

use Docker\Container;
use Docker\Json;

use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Exception\ServerErrorException;
use Docker\Exception\ContainerNotFoundException;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

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
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                return null;
            }

            throw $e;
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
        $request = $this->client->get(['/containers/{id}/json', ['id' => $container->getId()]]);
        $response = $request->send();

        $container->setRuntimeInformations($response->json());

        return $this;
    }

    /**
     * @param Docker\Container $container
     * 
     * @return Docker\Manager\ContainerManager
     */
    public function create(Container $container)
    {
        $request = $this->client->post('/containers/create');
        $request->setBody(Json::encode($container->getConfig()), 'application/json');

        $response = $request->send();

        if ($response->getStatusCode() === 500) {
            throw new ServerErrorException();
        }

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $container->setId($response->json()['Id']);

        $this->inspect($container);

        return $this;
    }

    /**
     * @param Docker\Container $container
     * 
     * @return Docker\Manager\ContainerManager
     */
    public function start(Container $container, array $hostConfig = array())
    {
        $request = $this->client->post(['/containers/{id}/start', ['id' => $container->getId()]]);
        $request->setBody(Json::encode($hostConfig), 'application/json');

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new ContainerNotFoundException($container->getId(), $e);
            }

            throw $e;
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
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
        $request = $this->client->post(['/containers/{id}/wait', ['id' => $container->getId()]]);
        $response = $request->send();

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new ContainerNotFoundException($container->getId(), $e);
            }

            throw $e;
        }

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $container->setExitCode($response->json()['StatusCode']);

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
        $request = $this->client->post(['/containers/{id}/stop?t={timeout}', [
            'id' => $container->getId(),
            'timeout' => $timeout
        ]]);

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new ContainerNotFoundException($container->getId(), $e);
            }

            throw $e;
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
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
        $request = $this->client->delete(['/containers/{id}?v={volumes}', [
            'id' => $container->getId(),
            'v' => $volumes
        ]]);

        try {
            $response = $request->send();
        } catch (ClientErrorResponseException $e) {
            if ($e->getResponse()->getStatusCode() === 404) {
                throw new ContainerNotFoundException($container->getId(), $e);
            }

            throw $e;
        }

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        return $this;
    }
}