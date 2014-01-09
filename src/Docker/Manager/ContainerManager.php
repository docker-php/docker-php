<?php

namespace Docker\Manager;

use Docker\Container;
use Docker\Json;

use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Exception\ContainerNotFoundException;

use Docker\Http\Client;

/**
 * Docker\Manager\ContainerManager
 */
class ContainerManager
{
    /**
     * @var Docker\Http\Client
     */
    private $client;

    /**
     * @param Docker\Http\Client
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
        $request = $this->client->get(['/containers/{id}/json', ['id' => $container->getId()]]);
        $response = $this->client->send($request);

        if ($response->getStatusCode() === 404) {
            throw new ContainerNotFoundException($container->getId());
        }

        $container->setRuntimeInformations($response->json(true));

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
        $request->setContent(Json::encode($container->getConfig()), 'application/json');

        $response = $this->client->send($request);

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $container->setId($response->json(true)['Id']);

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
        $request->setContent(Json::encode($hostConfig), 'application/json');

        $response = $this->client->send($request);

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
     * @param Docker\Container  $container Container to attach
     * @param callable          $callback  Callback to call when retrieving logs/stdin/stdout/stderr
     *
     * The callback need to respect this format : function($streamType, $output)
     *
     * Where $streamType will be 0 for STDIN, 1 for STDOUT, 2 for STDERR and $output will be the string of log
     *
     * @param boolean           $logs      Get the backlog of this container
     * @param boolean           $stream    Stream the response
     * @param boolean           $stdin     Get stdin log
     * @param boolean           $stdout    Get stdout log
     * @param boolean           $stderr    Get stderr log
     *
     * @return Docker\Manager\ContainerManager
     */
    public function attach(Container $container, $callback, $logs = true, $stream = true, $stdin = true, $stdout = true, $stderr = true)
    {
        $request = $this->client->post(['/containers/{id}/attach{?data*}', [
            'id'     => $container->getId(),
            'data' => [
                'logs'   => $logs,
                'stream' => $stream,
                'stdin'  => $stdin,
                'stdout' => $stdout,
                'stderr' => $stderr
            ]
        ]]);

        $response = $this->client->send($request);

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $response->read(function ($payload) use($response, $callback) {
            while (!empty($payload)) {
                $header  = substr($payload, 0, 8);
                $decoded = unpack('C1stream_type/C3/N1size', $header);
                $content = substr($payload, 8, $decoded['size']);
                $payload = substr($payload, 8 + $decoded['size']);

                $callback($decoded['stream_type'], $content);
            }
        });

        return $this;
    }

    /**
     * @param Docker\Container $container
     *
     * @return Docker\Manager\ContainerManager
     */
    public function wait(Container $container, $timeout = null)
    {
        $request = $this->client->post(['/containers/{id}/wait', ['id' => $container->getId()]]);
        $request->setTimeout($timeout);

        $response = $this->client->send($request);

        if ($response->getStatusCode() !== 200) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        $container->setExitCode($response->json(true)['StatusCode']);

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

        $response = $this->client->send($request);

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

        $response = $this->client->send($request);

        if ($response->getStatusCode() !== 204) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
        }

        return $this;
    }
}