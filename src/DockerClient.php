<?php

declare(strict_types=1);

namespace Docker;

use GuzzleHttp\Psr7\Uri;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\PluginClientFactory;
use Http\Client\HttpClient;
use Http\Client\Socket\Client as SocketHttpClient;
use Http\Message\MessageFactory\GuzzleMessageFactory;
use Psr\Http\Message\RequestInterface;

class DockerClient implements HttpClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(PluginClientFactory $pluginClientFactory, $socketClientOptions = [])
    {
        $messageFactory = new GuzzleMessageFactory();
        $socketClient = new SocketHttpClient($messageFactory, $socketClientOptions);
        $host = \preg_match('/unix:\/\//', $socketClientOptions['remote_socket']) ? 'http://localhost' : $socketClientOptions['remote_socket'];

        $this->httpClient = $pluginClientFactory->createClient($socketClient, [
            new ContentLengthPlugin(),
            new DecoderPlugin(),
            new AddHostPlugin(new Uri($host)),
        ]);
    }

    /**
     * (@inheritdoc}.
     */
    public function sendRequest(RequestInterface $request)
    {
        return $this->httpClient->sendRequest($request);
    }

    /**
     * @return DockerClient
     */
    public static function create(PluginClientFactory $pluginClientFactory = null)
    {
        $pluginClientFactory = $pluginClientFactory ?? new PluginClientFactory();

        return new self($pluginClientFactory, [
            'remote_socket' => 'unix:///var/run/docker.sock',
        ]);
    }

    /**
     * Create a docker client from environment variables.
     *
     * @throws \RuntimeException Throw exception when invalid environment variables are given
     *
     * @return DockerClient
     */
    public static function createFromEnv(PluginClientFactory $pluginClientFactory = null)
    {
        $pluginClientFactory = $pluginClientFactory ?? new PluginClientFactory();
        $options = [
            'remote_socket' => \getenv('DOCKER_HOST') ? \getenv('DOCKER_HOST') : 'unix:///var/run/docker.sock',
        ];

        if (\getenv('DOCKER_TLS_VERIFY') && 1 === \getenv('DOCKER_TLS_VERIFY')) {
            if (!\getenv('DOCKER_CERT_PATH')) {
                throw new \RuntimeException('Connection to docker has been set to use TLS, but no PATH is defined for certificate in DOCKER_CERT_PATH docker environnement variable');
            }

            $cafile = \getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'ca.pem';
            $certfile = \getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'cert.pem';
            $keyfile = \getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'key.pem';

            $stream_context = [
                'cafile' => $cafile,
                'local_cert' => $certfile,
                'local_pk' => $keyfile,
            ];

            if (\getenv('DOCKER_PEER_NAME')) {
                $stream_context['peer_name'] = \getenv('DOCKER_PEER_NAME');
            }

            $options['ssl'] = true;
            $options['stream_context_options'] = [
                'ssl' => $stream_context,
            ];
        }

        return new self($pluginClientFactory, $options);
    }
}
