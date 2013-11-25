<?php

namespace Docker;

use Docker\Manager\ContainerManager;

use Docker\Exception\UnexpectedStatusCodeException;

use Guzzle\Http\Client;
use Guzzle\Stream\PhpStreamRequestFactory;

class Docker
{
    private $client;
    private $containerManager;

    const BUILD_VERBOSE = false;
    const BUILD_QUIET = true;

    const BUILD_CACHE = true;
    const BUILD_NO_CACHE = false;

    /**
     * @param Guzzle\Http\Client                    $client
     * @param Guzzle\Stream\PhpStreamRequestFactory $factory
     */
    public function __construct(Client $client = null, PhpStreamRequestFactory $streamRequestFactory = null)
    {
        $this->client = $client ?: new Client('http://127.0.0.1:4243');
        $this->streamRequestFactory = $streamRequestFactory ?: new PhpStreamRequestFactory();
    }

    /**
     * @return Docker\Manager\ContainerManager
     */
    public function getContainerManager()
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->client);
        }

        return $this->containerManager;
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
        $request = $this->client->post(['/build{?data*}', ['data' => [
            'q' => $quiet,
            't' => $name,
            'nocache' => !$cache
        ]]]);

        $request->setBody($context->toStream(), 'application/tar');
        $response = $request->send();

        return $this->streamRequestFactory->fromRequest($request);
    }

    /**
     * @param Docker\Container $container
     * @param array $config
     * 
     * @return Docker\Image
     */
    public function commit(Container $container, $config = array())
    {
        if (isset($config['run'])) {
            $config['run'] = json_encode($config['run']);
        }

        $config['container'] = $container->getId();

        $request = $this->client->post(['/commit{?config*}', ['config' => $config]]);
        $response = $request->send();

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($reponse->getStatusCode());
        }

        $image = new Image();
        $image->setId($response->json()['Id']);

        return $image;
    }
}