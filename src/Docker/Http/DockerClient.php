<?php

namespace Docker\Http;

use Docker\Http\Adapter\DockerAdapter;
use GuzzleHttp\Client;

class DockerClient extends Client
{
    /**
     * @param array  $config     Config for http client (guzzle)
     * @param string $entrypoint Docker entrypoint
     * @param array  $context    Stream context options
     * @param bool   $useTls     Use tls
     */
    public function __construct(array $config = [], $entrypoint = null, $context = null, $useTls = false)
    {
        if (null === $entrypoint) {
            // @TODO Force entrypoint in stable version
            if (!getenv('DOCKER_HOST')) {
                trigger_error('Not specified entrypoint is deprecated and will throw an exception in the stable version', E_USER_DEPRECATED);
            }

            $entrypoint = getenv('DOCKER_HOST') ? getenv('DOCKER_HOST') : 'unix:///var/run/docker.sock';
        }

        if (is_array($context)) {
            $context = stream_context_create($context);
        }

        if (!isset($config['adapter'])) {
            if (isset($config['message_factory'])) {
                $messageFactory = $config['message_factory'];
            } else {
                $messageFactory = new MessageFactory();
            }

            $config['message_factory'] = $messageFactory;
            $config['adapter']         = new DockerAdapter($messageFactory, $entrypoint, $context, $useTls);
        }

        parent::__construct($config);
    }

    /**
     * Create a basic docker client with default configuration
     *
     * @param array $config Config for http client (guzzle)
     *
     * @return DockerClient
     */
    public static function create(array $config = [])
    {
        return new self($config, 'unix:///var/run/docker.sock', null);
    }

    /**
     * Create a docker client from environnement variable
     *
     * @param array $config Config for the http client (guzzle)
     *
     * @return DockerClient
     *
     * @throws \RuntimeException Throw exception when invalid environment variables are given
     */
    public static function createWithEnv(array $config = [])
    {
        $entrypoint = getenv('DOCKER_HOST') ? getenv('DOCKER_HOST') : 'unix:///var/run/docker.sock';
        $context    = null;
        $useTls     = false;

        if (getenv('DOCKER_TLS_VERIFY') && getenv('DOCKER_TLS_VERIFY') == 1) {
            if (!getenv('DOCKER_CERT_PATH')) {
                throw new \RuntimeException('Connection to docker has been set to use TLS, but no PATH is defined for certificate in DOCKER_CERT_PATH docker environnement variable');
            }

            $useTls = true;
            $cafile = getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'ca.pem';
            $certfile = getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'cert.pem';
            $keyfile = getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'key.pem';
            $peername = getenv('DOCKER_PEER_NAME') ? getenv('DOCKER_PEER_NAME') : 'boot2docker';
            $fullcert = tempnam(sys_get_temp_dir(), 'docker-certfile');

            file_put_contents($fullcert, file_get_contents($certfile));
            file_put_contents($fullcert, file_get_contents($keyfile), FILE_APPEND);

            $context = stream_context_create([
                'ssl' => [
                    'cafile' => $cafile,
                    'local_cert' => $fullcert,
                    'peer_name' => $peername,
                ],
            ]);
        }

        return new self($config, $entrypoint, $context, $useTls);
    }
}
