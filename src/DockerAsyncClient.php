<?php

declare(strict_types=1);

namespace Docker;

use Amp\Artax\Client;
use Amp\Artax\DefaultClient;
use Amp\Artax\HttpSocketPool;
use Amp\Artax\Request;
use Amp\CancellationToken;
use Amp\Promise;
use Amp\Socket\BasicSocketPool;
use Amp\Socket\ClientTlsContext;
use Docker\Amp\FixedSocketPool;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DockerAsyncClient implements Client
{
    private $client;

    public function __construct(array $options = [])
    {
        $options = $this->resolveOptions($options);
        $socketPool = new HttpSocketPool(new FixedSocketPool($options['remote_socket'], new BasicSocketPool()));
        $this->client = new DefaultClient(null, $socketPool, $options['ssl']);
    }

    public function request($uriOrRequest, array $options = [], CancellationToken $cancellation = null): Promise
    {
        if ($uriOrRequest instanceof Request) {
            $uriOrRequest = $uriOrRequest->withUri('http://localhost'.$uriOrRequest->getUri());
        }

        return $this->client->request($uriOrRequest, $options, $cancellation);
    }

    protected function resolveOptions(array $options = []): array
    {
        $resolver = new OptionsResolver();

        $resolver->setDefaults([
            'ssl' => null,
        ]);

        $resolver->setRequired([
            'remote_socket',
        ]);

        $resolver->setAllowedTypes('ssl', ['null', ClientTlsContext::class]);

        return $resolver->resolve($options);
    }

    public static function createFromEnv(): self
    {
        $options = [
            'remote_socket' => \getenv('DOCKER_HOST') ? \getenv('DOCKER_HOST') : 'unix:///var/run/docker.sock',
        ];

        if (\getenv('DOCKER_TLS_VERIFY') && '1' === \getenv('DOCKER_TLS_VERIFY')) {
            if (!\getenv('DOCKER_CERT_PATH')) {
                throw new \RuntimeException('Connection to docker has been set to use TLS, but no PATH is defined for certificate in DOCKER_CERT_PATH docker environnement variable');
            }

            $tlsContext = new ClientTlsContext();

            $cafile = \getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'ca.pem';
            $certfile = \getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'cert.pem';
            $keyfile = \getenv('DOCKER_CERT_PATH').DIRECTORY_SEPARATOR.'key.pem';

            $tlsContext = $tlsContext->withCaFile($cafile);

//            $stream_context = [
//                'cafile'        => $cafile,
//                'local_cert'    => $certfile,
//                'local_pk'      => $keyfile,
//            ];

            if (\getenv('DOCKER_PEER_NAME')) {
                $tlsContext = $tlsContext->withPeerName(\getenv('DOCKER_PEER_NAME'));
            }

            $options['ssl'] = $tlsContext;
        }

        return new static($options);
    }
}
