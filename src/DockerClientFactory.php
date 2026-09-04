<?php

declare(strict_types=1);

namespace Docker;

use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\HeaderDefaultsPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Client\Socket\Client;
use Http\Discovery\Psr17FactoryDiscovery;

final class DockerClientFactory
{
    public static function create(array $config = [], ?PluginClientFactory $pluginClientFactory = null): PluginClient
    {
        if (!\array_key_exists('remote_socket', $config)) {
            $config['remote_socket'] = 'unix:///var/run/docker.sock';
        }

        $socketClient = new Client($config);

        $uriFactory = Psr17FactoryDiscovery::findUriFactory();
        $host = preg_match('/unix:\/\//', $config['remote_socket']) ? 'http://localhost' : $config['remote_socket'];
        $dockerApiVersion = ltrim(getenv('DOCKER_API_VERSION') ?: 'v1.45', '/');
        if (!str_starts_with($dockerApiVersion, 'v')) {
            $dockerApiVersion = 'v'.$dockerApiVersion;
        }

        $pluginClientFactory ??= new PluginClientFactory();

        return $pluginClientFactory->createClient(
            $socketClient,
            [
                new ContentLengthPlugin(),
                new DecoderPlugin(),
                new AddPathPlugin($uriFactory->createUri('/'.$dockerApiVersion)),
                new AddHostPlugin($uriFactory->createUri($host)),
                new HeaderDefaultsPlugin([
                    'host' => parse_url($host, \PHP_URL_HOST),
                ]),
            ],
            [
                'client_name' => 'docker-client',
            ]
        );
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
