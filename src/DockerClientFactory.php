<?php

declare(strict_types=1);

namespace Docker;

use Docker\Http\InteractiveHttpClient;
use Docker\Http\StreamingDecoderPlugin;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Client\Socket\Client;
use Http\Discovery\Psr17FactoryDiscovery;

final class DockerClientFactory
{
    /**
     * The api_version option overrides DOCKER_API_VERSION; other options are
     * passed to the socket client. Neither setting changes the generated models.
     */
    public static function create(array $config = [], ?PluginClientFactory $pluginClientFactory = null): PluginClient
    {
        if (\array_key_exists('api_version', $config)) {
            if (!\is_string($config['api_version']) || !preg_match('#^/?v?[0-9]+\.[0-9]+$#D', $config['api_version'])) {
                throw new \InvalidArgumentException('api_version must be a Docker API version such as 1.45 or v1.45.');
            }
            $dockerApiVersion = $config['api_version'];
            unset($config['api_version']);
        } else {
            $dockerApiVersion = getenv('DOCKER_API_VERSION') ?: 'v1.45';
        }
        $dockerApiVersion = ltrim($dockerApiVersion, '/');
        if (!str_starts_with($dockerApiVersion, 'v')) {
            $dockerApiVersion = 'v'.$dockerApiVersion;
        }

        if (!\array_key_exists('remote_socket', $config)) {
            $config['remote_socket'] = 'unix:///var/run/docker.sock';
        }

        $uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $unixSocket = str_starts_with($config['remote_socket'], 'unix://');
        $host = $uriFactory->createUri($unixSocket ? 'http://localhost' : $config['remote_socket']);
        $scheme = $unixSocket ? 'unix' : $host->getScheme();

        if (\in_array($scheme, ['http', 'https', 'tcp'], true)) {
            if ('https' === $scheme) {
                $config['ssl'] = true;
            }

            if ('http' === $scheme || 'https' === $scheme) {
                // The socket client negotiates TLS after opening the TCP connection.
                $port = $host->getPort() ?? ('https' === $scheme ? 443 : 80);
                $config['remote_socket'] = 'tcp://'.$host->getHost().':'.$port;
            }

            $host = $host->withScheme(($config['ssl'] ?? false) ? 'https' : 'http');
        }

        $socketClient = new Client($config);

        $pluginClientFactory ??= new PluginClientFactory();

        return $pluginClientFactory->createClient(
            $socketClient,
            [
                new ContentLengthPlugin(),
                new StreamingDecoderPlugin(),
                new AddPathPlugin($uriFactory->createUri('/'.$dockerApiVersion)),
                new AddHostPlugin($host),
                new HeaderDefaultsPlugin([
                    'host' => $host->withUserInfo('')->getAuthority(),
                ]),
            ],
            [
                'client_name' => 'docker-client',
            ]
        );
    }

    /** Socket transport with opt-in interactive exec support. */
    public static function createInteractive(array $config = [], ?PluginClientFactory $pluginClientFactory = null): InteractiveHttpClient
    {
        return new InteractiveHttpClient(self::create($config, $pluginClientFactory));
    }

    public static function createInteractiveFromEnv(?PluginClientFactory $pluginClientFactory = null): InteractiveHttpClient
    {
        return new InteractiveHttpClient(self::createFromEnv($pluginClientFactory));
    }

    public static function createFromEnv(?PluginClientFactory $pluginClientFactory = null): PluginClient
    {
        $options = [
            'remote_socket' => getenv('DOCKER_HOST') ? getenv('DOCKER_HOST') : 'unix:///var/run/docker.sock',
        ];

        if (getenv('DOCKER_TLS_VERIFY') && '1' === getenv('DOCKER_TLS_VERIFY')) {
            if (!getenv('DOCKER_CERT_PATH')) {
                throw new \RuntimeException('Connection to docker has been set to use TLS, but no PATH is defined for certificate in DOCKER_CERT_PATH docker environment variable');
            }

            $cafile = getenv('DOCKER_CERT_PATH').\DIRECTORY_SEPARATOR.'ca.pem';
            $certfile = getenv('DOCKER_CERT_PATH').\DIRECTORY_SEPARATOR.'cert.pem';
            $keyfile = getenv('DOCKER_CERT_PATH').\DIRECTORY_SEPARATOR.'key.pem';

            $stream_context = [
                'cafile' => $cafile,
                'local_cert' => $certfile,
                'local_pk' => $keyfile,
            ];

            if (getenv('DOCKER_PEER_NAME')) {
                $stream_context['peer_name'] = getenv('DOCKER_PEER_NAME');
            }

            $options['ssl'] = true;
            $options['stream_context_options'] = [
                'ssl' => $stream_context,
            ];
        }

        return self::create($options, $pluginClientFactory);
    }
}
