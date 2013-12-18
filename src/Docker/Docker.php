<?php

namespace Docker;

use Docker\Manager\ContainerManager;
use Docker\Exception\UnexpectedStatusCodeException;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ServerErrorResponseException;
use Guzzle\Stream\PhpStreamRequestFactory;
use Guzzle\Plugin\Log\LogPlugin;

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
     * @var Guzzle\Http\Client
     */
    private $client;

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
    public function __construct(Client $client = null, PhpStreamRequestFactory $streamRequestFactory = null)
    {
        $this->client = $client ?: new Client('http://127.0.0.1:4243');
        $this->streamRequestFactory = $streamRequestFactory ?: new PhpStreamRequestFactory();
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
            $this->containerManager = new ContainerManager($this->client);
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
        $fp = stream_socket_client('tcp://127.0.0.1:4243');

        $query = http_build_query([
            'q' => (integer) $quiet,
            't' => $name,
            'nocache' => (integer) !$cache,
        ]);

        $length = mb_strlen($context->toTar());

        fwrite($fp, "POST /build?$query HTTP/1.0\r\n");
        fwrite($fp, "Content-Type: application/tar\r\n");
        fwrite($fp, "Content-Length: $length\r\n");
        fwrite($fp, "\r\n");
        fwrite($fp, $context->toTar());

        $content = stream_get_contents($fp);

        fclose($fp);
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

        $request = $this->client->post(['/commit{?config*}', ['config' => $config]]);
        $response = $request->send();

        if ($response->getStatusCode() !== 201) {
            throw new UnexpectedStatusCodeException($response->getStatusCode());
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
