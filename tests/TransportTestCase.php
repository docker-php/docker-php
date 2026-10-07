<?php

declare(strict_types=1);

namespace Docker\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

/**
 * Real connections to short-lived local fixtures, never a Docker daemon.
 */
abstract class TransportTestCase extends TestCase
{
    protected string $directory;
    private ?Process $server = null;
    private array $environment = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->directory = sys_get_temp_dir().'/docker-test-'.bin2hex(random_bytes(6));
        mkdir($this->directory, 0700);

        foreach (['DOCKER_HOST', 'DOCKER_API_VERSION', 'DOCKER_TLS_VERIFY', 'DOCKER_CERT_PATH', 'DOCKER_PEER_NAME'] as $name) {
            $this->environment[$name] = getenv($name);
            putenv($name);
        }
    }

    protected function tearDown(): void
    {
        $this->server?->stop(0);
        (new Filesystem())->remove($this->directory);
        foreach ($this->environment as $name => $value) {
            putenv(false === $value ? $name : $name.'='.$value);
        }
        parent::tearDown();
    }

    protected function startServer(array $options = [], bool $unix = false, string $fixture = 'socket-server.php'): string
    {
        $socket = $this->directory.'/sock';
        $options['address'] = $unix ? 'unix://'.$socket : 'tcp://127.0.0.1:0';
        $this->server = new Process([\PHP_BINARY, '-d', 'display_errors=stderr', __DIR__.'/fixtures/'.$fixture, json_encode($options, \JSON_THROW_ON_ERROR)]);
        $this->server->setTimeout(10);
        $this->server->start();
        $ready = $this->server->waitUntil(fn () => str_contains($this->server->getOutput(), "\n"));
        $this->assertTrue($ready, $this->server->getErrorOutput());
        $message = json_decode(explode("\n", $this->server->getOutput())[0], true, 512, \JSON_THROW_ON_ERROR);

        return $unix ? $socket : $message['address'];
    }

    protected function serverResult(int $index = 0): array
    {
        $this->server->wait();
        $this->assertTrue($this->server->isSuccessful(), $this->server->getErrorOutput());

        return json_decode(explode("\n", $this->server->getOutput())[$index + 1], true, 512, \JSON_THROW_ON_ERROR);
    }

    protected function serverTlsOptions(): array
    {
        return [
            'local_cert' => $this->directory.'/server.pem',
            'local_pk' => $this->directory.'/server-key.pem',
        ];
    }

    protected function createCertificates(string $serverNames = 'DNS:docker.test,DNS:localhost,IP:127.0.0.1'): void
    {
        $config = $this->directory.'/openssl.cnf';
        file_put_contents(
            $config,
            str_replace(
                'SERVER_NAMES',
                $serverNames,
                <<<'CONFIG'
                    [req]
                    distinguished_name = dn
                    [dn]
                    [ca]
                    basicConstraints = critical,CA:TRUE
                    keyUsage = critical,keyCertSign,cRLSign
                    [server]
                    basicConstraints = critical,CA:FALSE
                    keyUsage = critical,digitalSignature,keyEncipherment
                    extendedKeyUsage = serverAuth
                    subjectAltName = SERVER_NAMES
                    [client]
                    basicConstraints = critical,CA:FALSE
                    keyUsage = critical,digitalSignature,keyEncipherment
                    extendedKeyUsage = clientAuth
                    CONFIG
            )
        );
        $options = ['config' => $config, 'private_key_bits' => 2048, 'digest_alg' => 'sha256'];
        $caKey = openssl_pkey_new($options);
        $caRequest = openssl_csr_new(['commonName' => 'Docker PHP test CA'], $caKey, $options);
        $ca = openssl_csr_sign($caRequest, null, $caKey, 1, $options + ['x509_extensions' => 'ca'], 1);
        $this->assertNotFalse($ca);
        openssl_x509_export_to_file($ca, $this->directory.'/ca.pem');

        foreach (['server' => 'docker.test', 'client' => 'docker-client'] as $type => $name) {
            $key = openssl_pkey_new($options);
            $request = openssl_csr_new(['commonName' => $name], $key, $options);
            $certificate = openssl_csr_sign($request, $ca, $caKey, 1, $options + ['x509_extensions' => $type], 'server' === $type ? 2 : 3);
            $this->assertNotFalse($certificate);
            openssl_x509_export_to_file($certificate, $this->directory.('server' === $type ? '/server.pem' : '/cert.pem'));
            openssl_pkey_export_to_file($key, $this->directory.('server' === $type ? '/server-key.pem' : '/key.pem'), null, $options);
        }
    }
}
