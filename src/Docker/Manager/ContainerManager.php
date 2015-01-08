<?php

namespace Docker\Manager;

use Docker\Container;
use Docker\Http\Stream\InteractiveStream;
use Docker\Json;
use Docker\Exception\UnexpectedStatusCodeException;
use Docker\Exception\ContainerNotFoundException;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

/**
 * Docker\Manager\ContainerManager
 */
class ContainerManager
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * @param \GuzzleHttp\Client
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
     * @throws \GuzzleHttp\Exception\RequestException
     * @throws \Docker\Exception\ContainerNotFoundException
     *
     * @return ContainerManager
     */
    public function inspect(Container $container)
    {
        try {
            $response = $this->client->get(['/containers/{id}/json', ['id' => $container->getId()]]);
        } catch (RequestException $e) {
            if ($e->hasResponse() && $e->getResponse()->getStatusCode() == "404") {
                throw new ContainerNotFoundException($container->getId(), $e);
            }

            throw $e;
        }

        $container->setRuntimeInformations($response->json());

        return $response->json();
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
            'headers'      => array('content-type' => 'application/json'),
        ));

        if ($response->getStatusCode() !== "201") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $container->setId($response->json()['Id']);

        return $this;
    }

    /**
     * @param \Docker\Container $container
     * @param array             $hostConfig Config when starting the container (for port binding e.g.)
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function start(Container $container, array $hostConfig = array())
    {
        $response = $this->client->post(['/containers/{id}/start', ['id' => $container->getId()]], array(
            'body'         => Json::encode($hostConfig),
            'headers'      => array('content-type' => 'application/json'),
            'wait'         => true,
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
     * @param \Docker\Container $container
     * @param callable          $attachCallback Callback to read the attach response
     *                                          If set to null no attach call will be made, otherwise callback must respect the format for the readAttach
     *                                          method in Docker\Http\Response class
     *
     * @param array   $hostConfig Config when starting the container (for port binding e.g.)
     * @param boolean $daemon     Do not wait for run to finish
     * @param integer $timeout    Timeout pass to the attach call
     *
     * @return boolean|null Return true when the process want well, false if an error append during the run process, or null when daemon is set to true
     */
    public function run(Container $container, callable $attachCallback = null, array $hostConfig = array(), $daemon = false, $timeout = null)
    {
        $this->create($container);

        if (null !== $attachCallback) {
            $attachResponse = $this->attach($container, $attachCallback, true, true, true, true, true, $timeout);
        }

        $this->start($container, $hostConfig);

        if (!$daemon) {
            if (isset($attachResponse)) {
                $attachResponse->getBody()->getContents();
            }

            $this->wait($container);

            return ($container->getExitCode() == 0);
        }

        return null;
    }

    /**
     * Attach a container to a callback to read logs
     *
     * @param \Docker\Container $container Container to attach
     *
     * Where $streamType will be 0 for STDIN, 1 for STDOUT, 2 for STDERR and $output will be the string of log
     *
     * @param callable $callback Callback to attach
     * @param boolean  $logs     Get the backlog of this container
     * @param boolean  $stream   Stream the response
     * @param boolean  $stdin    Get stdin log
     * @param boolean  $stdout   Get stdout log
     * @param boolean  $stderr   Get stderr log
     * @param integer  $timeout  Timeout when
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function attach(Container $container, callable $callback, $logs = true, $stream = true, $stdin = true, $stdout = true, $stderr = true, $timeout = null)
    {
        $response = $this->client->post(['/containers/{id}/attach{?data*}', [
            'id'     => $container->getId(),
            'data' => [
                'logs'   => $logs,
                'stream' => $stream,
                'stdin'  => $stdin,
                'stdout' => $stdout,
                'stderr' => $stderr,
            ]
        ]], array(
            'timeout'  => $timeout !== null ? $timeout : $this->client->getDefaultOption('timeout'),
            'callback' => $callback,
        ));

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response;
    }

    /**
     * Interact with a container
     *
     * Create a websocket connection which allows to send data on stdin
     *
     * @param Container $container
     * @param boolean   $logs      Get the backlog of this container
     * @param boolean   $stream    Stream the response
     * @param boolean   $stdin     Get stdin log
     * @param boolean   $stdout    Get stdout log
     * @param boolean   $stderr    Get stderr log
     *
     * @return InteractiveStream
     */
    public function interact(Container $container, $logs = true, $stream = true, $stdin = true, $stdout = true, $stderr = true)
    {
        $response = $this->client->get(['/containers/{id}/attach/ws{?data*}', [
            'id' => $container->getId(),
            'data' => [
                'logs'   => $logs,
                'stream' => $stream,
                'stdin'  => $stdin,
                'stdout' => $stdout,
                'stderr' => $stderr,
            ]
        ]], array(
            'headers' => array(
                'Origin' => 'php://docker-php',
                'Upgrade' => 'websocket',
                'Connection' => 'Upgrade',
                'Sec-WebSocket-Version' => '13',
                'Sec-WebSocket-Key' => base64_encode(uniqid()),
            ),
        ));

        return new InteractiveStream($response->getBody());
    }

    /**
     * Wait for a container to finish
     *
     * @param \Docker\Container $container
     * @param integer|null      $timeout
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function wait(Container $container, $timeout = null)
    {
        $response = $this->client->post(['/containers/{id}/wait', ['id' => $container->getId()]], array(
            'timeout' => null === $timeout ? $this->client->getDefaultOption('timeout') : $timeout,
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
     * @param integer           $timeout
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function stop(Container $container, $timeout = 5)
    {
        $response = $this->client->post(['/containers/{id}/stop?t={timeout}', [
            'id' => $container->getId(),
            'timeout' => $timeout,
            'wait' => true,
        ]]);

        if ($response->getStatusCode() !== "204" && $response->getStatusCode() !== "304") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $this->inspect($container);

        return $this;
    }

    /**
     * Delete a container from docker server
     *
     * @param \Docker\Container $container
     * @param boolean           $volumes
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \Docker\Manager\ContainerManager
     */
    public function remove(Container $container, $volumes = false)
    {
        $response = $this->client->delete(['/containers/{id}?v={volumes}', [
            'id' => $container->getId(),
            'v' => $volumes,
            'wait' => true
        ]]);

        if ($response->getStatusCode() !== "204") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $this;
    }

    /**
     * List process running inside a container
     *
     * @param Container $container
     * @param string    $psArgs
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return array
     */
    public function top(Container $container, $psArgs = "aux")
    {
        $response = $this->client->get(['/containers/{id}/top?ps_args={ps_args}', [
            'id' => $container->getId(),
            'ps_args' => $psArgs
        ]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        $processes = array();
        $data      = $response->json();

        $keys = $data['Titles'];

        foreach ($data['Processes'] as $values) {
            $processes[] = array_combine($keys, $values);
        }

        return $processes;
    }

    /**
     * Get changes on a container filesystem
     *
     * @param Container $container
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return array
     */
    public function changes(Container $container)
    {
        $response = $this->client->get(['/containers/{id}/changes', [
            'id' => $container->getId()
        ]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response->json();
    }

    /**
     * Export a container to a tar
     *
     * @param Container $container
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     *
     * @return \GuzzleHttp\Stream\Stream
     */
    public function export(Container $container)
    {
        $response = $this->client->get(['/containers/{id}/export', [
            'id' => $container->getId()
        ]]);

        if ($response->getStatusCode() !== "200") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }

        return $response->getBody();
    }

    /**
     * Get logs from a container
     *
     * @param Container $container
     * @param bool $follow
     * @param bool $stdout
     * @param bool $stderr
     * @param bool $timestamp
     * @param string $tail
     *
     * @return array
     */
    public function logs(Container $container, $follow = false, $stdout = false, $stderr = false, $timestamp = false, $tail = "all")
    {
        $logs = array();

        $callback = function ($output, $type) use(&$logs) {
            $logs[] = array('type' => $type, 'output' => $output);
        };

        $this->client->get(['/containers/{id}/logs{?data*}', [
            'id' => $container->getId(),
            'data' => [
                'follow' => (int)$follow,
                'stdout' => (int)$stdout,
                'stderr' => (int)$stderr,
                'timestamps' => (int)$timestamp,
                'tail' => $tail,
            ],
        ]], [
            'callback' => $callback,
            'wait'     => true,
        ]);

        return $logs;
    }

    /**
     * Restart a container
     *
     * @param Container $container
     * @param integer   $timeBeforeKill number of seconds to wait before killing the container
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     */
    public function restart(Container $container, $timeBeforeKill = 5)
    {
        $response = $this->client->post(['/containers/{id}/restart?t={time}', [
            'id' => $container->getId(),
            'time' => $timeBeforeKill
        ]]);

        if ($response->getStatusCode() !== "204") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }
    }

    /**
     * Send a signal to container
     *
     * @param Container $container
     * @param string $signal
     *
     * @throws \Docker\Exception\UnexpectedStatusCodeException
     */
    public function kill(Container $container, $signal = "SIGKILL")
    {
        $response = $this->client->post(['/containers/{id}/kill?signal={signal}', [
            'id' => $container->getId(),
            'signal' => $signal
        ]]);

        if ($response->getStatusCode() !== "204") {
            throw UnexpectedStatusCodeException::fromResponse($response);
        }
    }
}
