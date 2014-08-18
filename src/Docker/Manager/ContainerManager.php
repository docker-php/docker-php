<?php

namespace Docker\Manager;

use Docker\Container;
use Docker\Json;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Exception\ContainerNotFoundException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;

/**
 * Docker\Manager\ContainerManager
 */
class ContainerManager
{
    /**
     * @var \Docker\Http\Client
     */
    private $client;

    /**
     * @param \Docker\Http\Client
     */
    public function __construct(HttpClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get all containers from the docker daemon
     *
     * @param array $params an array of query parameters
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return Container[]
     */
    public function findAll(array $params = array())
    {
        $response = $this->client->get('/containers/json', [
            'query' => $params
        ]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $coll = [];

        $containers = $response->json();

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
     * Find a container by its id
     *
     * @param string $id
     *
     * @return \Docker\Container|null
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
     * Inspect a container
     *
     * @param \Docker\Container $container
     *
     * @throws \Docker\Exception\ContainerNotFoundException
     *
     * @return ContainerManager
     */
    public function inspect(Container $container)
    {
        try {
            $response = $this->client->get(['/containers/{id}/json', ['id' => $container->getId()]]);
        } catch (ClientException $e) {
            if ($e->getResponse()->getStatusCode() == "404") {
                throw new ContainerNotFoundException($container->getId(), $e);
            }

            throw $e;
        }

        $container->setRuntimeInformations($response->json());

        return $this;
    }

    /**
     * Create a container (do not start it)
     *
     * @param \Docker\Container $container
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function create(Container $container)
    {
        $response = $this->client->post(['/containers/create{?data*}', [
            'data' => [
                'name' => $container->getName(),
            ],
        ]],
        array(
            'body'         => Json::encode($container->getConfig()),
            'headers'      => array('content-type' => 'application/json')
        ));

        if ($response->getStatusCode() !== "201") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $container->setId($response->json()['Id']);

        return $this;
    }

    /**
     * @param \Docker\Container $container
     * @param array             $hostConfig     Config when starting the container (for port binding e.g.)
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function start(Container $container, array $hostConfig = array())
    {
        $response = $this->client->post(['/containers/{id}/start', ['id' => $container->getId()]], array(
            'body'         => Json::encode($hostConfig),
            'headers'      => array('content-type' => 'application/json')
        ));

        if ($response->getStatusCode() !== "204") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $this->inspect($container);

        return $this;
    }

    /**
     * Run a container (create, attach, start and wait)
     *
     * @param \Docker\Container  $container
     * @param callable          $attachCallback Callback to read the attach response
     *   If set to null no attach call will be made, otherwise callback must respect the format for the readAttach
     *   method in Docker\Http\Response class
     *
     * @param array             $hostConfig     Config when starting the container (for port binding e.g.)
     * @param boolean           $daemon         Do not wait for run to finish
     * @param integer           $timeout        Timeout pass to the attach call
     *
     * @return boolean|null Return true when the process want well, false if an error append during the run process, or null when daemon is set to true
     */
    public function run(Container $container, callable $attachCallback = null, array $hostConfig = array(), $daemon = false, $timeout = null)
    {
        $this->create($container);

        if (null !== $attachCallback) {
            $attachResponse = $this->attach($container, true, true, true, true, true, $timeout);
        }

        $this->start($container, $hostConfig);

        if (null !== $attachCallback && $attachResponse) {
            $attachResponse->getBody()->readWithCallback($attachCallback);
        }

        if (!$daemon) {
            $this->wait($container);

            return ($container->getExitCode() == 0);
        }

        return null;
    }

    /**
     * @param \Docker\Container  $container Container to attach
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
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Http\Response Re
     */
    public function attach(Container $container, $logs = true, $stream = true, $stdin = true, $stdout = true, $stderr = true, $timeout = null)
    {
        $response = $this->client->post(['/containers/{id}/attach{?data*}', [
            'id'     => $container->getId(),
            'data' => [
                'logs'   => $logs,
                'stream' => $stream,
                'stdin'  => $stdin,
                'stdout' => $stdout,
                'stderr' => $stderr
            ]
        ]], array(
            'timeout' => $timeout !== null ? $timeout : $this->client->getDefaultOption('timeout')
        ));

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response;
    }

    /**
     * Wait for a container to finish
     *
     * @param \Docker\Container $container
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function wait(Container $container, $timeout = null)
    {
        $response = $this->client->post(['/containers/{id}/wait', ['id' => $container->getId()]], array(
            'timeout' => null === $timeout ? $this->client->getDefaultOption('timeout') : $timeout
        ));

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $container->setExitCode($response->json()['StatusCode']);

        $this->inspect($container);

        return $this;
    }

    /**
     * Stop a running container
     *
     * @param \Docker\Container $container
     * @param integer          $timeout
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function stop(Container $container, $timeout = 5)
    {
        $response = $this->client->post(['/containers/{id}/stop?t={timeout}', [
            'id' => $container->getId(),
            'timeout' => $timeout
        ]]);

        if ($response->getStatusCode() !== "204") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $this->inspect($container);

        return $this;
    }

    /**
     * Delete a container from docker server
     *
     * @param \Docker\Container  $container
     * @param boolean           $volumes
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function remove(Container $container, $volumes = false)
    {
        $response = $this->client->delete(['/containers/{id}?v={volumes}', [
            'id' => $container->getId(),
            'v' => $volumes
        ]]);

        if ($response->getStatusCode() !== "204") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $this;
    }
}