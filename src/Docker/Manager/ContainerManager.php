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

    public function findAll()
    {
        $request = $this->client->get('/containers/json');
        $response = $this->client->send($request);
        $response->read();

        if ($response->getStatusCode() !== 200) {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $coll = [];

        $containers = $response->json(true);

        if (!is_array($containers)) {
            return [];
        }

        foreach ($containers as $data) {
            $container = new Container();
            $container->setId($data['Id']);
            $container->setImage($data['Image']);
            $container->setCmd((array) $data['Command']);

            $container->setData($data);

            $coll[] = $container;
        }

        return $coll;
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
            throw UnexpectedStatusCodeException::fromResponse($response);
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
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $this->inspect($container);

        return $this;
    }

    /**
     * @param Docker\Container  $container
     * @param callable          $attachCallback Callback to read the attach response
     *
     * If set to null no attach call will be made, otherwise callback must respect the format for the readAttach
     * method in Docker\Http\Response class
     *
     * @param array             $hostConfig     Config when starting the container (for port binding e.g.)
     * @param boolean           $daemon         Do not wait for run to finish
     * @param integer           $timeout        Timeout pass to the attach call
     *
     * @return boolean Return true when the process want well, false if an error append during the run process, or null when daemon is set to true
     */
    public function run(Container $container, callable $attachCallback = null, array $hostConfig = array(), $daemon = false, $timeout = null)
    {
        $this->create($container);

        if (null !== $attachCallback) {
            $attachResponse = $this->attach($container, true, true, true, true, true, $timeout);
        }

        $this->start($container, $hostConfig);

        if (null !== $attachCallback) {
            $attachResponse->readAttach($attachCallback);
        }

        if (!$daemon) {
            $this->wait($container);

            return ($container->getExitCode() == 0);
        }

        return null;
    }

    /**
     * @param Docker\Container  $container Container to attach
     *
     * Where $streamType will be 0 for STDIN, 1 for STDOUT, 2 for STDERR and $output will be the string of log
     *
     * @param boolean           $logs      Get the backlog of this container
     * @param boolean           $stream    Stream the response
     * @param boolean           $stdin     Get stdin log
     * @param boolean           $stdout    Get stdout log
     * @param boolean           $stderr    Get stderr log
     * @param integer           $timeout   Timeout when
     *
     * @return Docker\Http\Response Re
     */
    public function attach(Container $container, $logs = true, $stream = true, $stdin = true, $stdout = true, $stderr = true, $timeout = null)
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

        $request->setProtocolVersion('1.1');
        $request->setTimeout($timeout);

        $response = $this->client->send($request, false);

        if ($response->getStatusCode() !== 200) {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response;
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
            throw UnexpectedStatusCodeException::fromResponse($response);
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
            throw UnexpectedStatusCodeException::fromResponse($response);
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
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $this;
    }
}