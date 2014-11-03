<?php

namespace Docker\Http;

use Docker\Http\Adapter\DockerAdapter;
use GuzzleHttp\Client;
use GuzzleHttp\Message\MessageFactory;

class DockerClient extends Client
{
    public function __construct(array $config = [], $entrypoint = null)
    {
        if (null === $entrypoint) {
            // @TODO Force entrypoint in stable version
            if (!getenv('DOCKER_HOST')) {
                trigger_error('Not specified entrypoint is deprecated and will throw an exception in the stable version', E_USER_DEPRECATED);
            }

            $entrypoint = getenv('DOCKER_HOST') ? getenv('DOCKER_HOST') : 'unix:///var/run/docker.sock';
        }

        if (!isset($config['adapter'])) {
            if (isset($config['message_factory'])) {
                $messageFactory = $config['message_factory'];
            } else {
                $messageFactory = new MessageFactory();
            }

            $config['message_factory'] = $messageFactory;
            $config['adapter']         = new DockerAdapter($messageFactory, $entrypoint);
        }

        parent::__construct($config);
    }
}
