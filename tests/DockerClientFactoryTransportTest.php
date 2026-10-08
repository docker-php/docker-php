<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\DockerClientFactory;
use Http\Client\Socket\Exception\SSLConnectionException;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;

class DockerClientFactoryTransportTest extends TransportTestCase
{
    public static function transports(): array
    {
        return [
            'Unix socket' => ['unix'],
            'TCP socket' => ['tcp'],
            'HTTP URL' => ['http'],
            'HTTPS URL' => ['https'],
        ];
    }

    #[DataProvider('transports')]
    public function testRealConnection(string $scheme): void
    {
        $serverConfig = [];
        $clientConfig = ['timeout' => 3000];
        if ('https' === $scheme) {
            $this->createCertificates();
            $serverConfig['ssl'] = $this->serverTlsOptions();
            $clientConfig['stream_context_options']['ssl']['cafile'] = $this->directory.'/ca.pem';
        }
        $address = $this->startServer($serverConfig, 'unix' === $scheme);
        $clientConfig['remote_socket'] = $scheme.'://'.$address;
        $response = DockerClientFactory::create($clientConfig)->sendRequest(new Request('GET', '/_ping'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getBody()->getContents());
        $request = $this->serverResult()['request'];
        $this->assertStringStartsWith('GET /v'.DockerClientFactory::defaultApiVersion()."/_ping HTTP/1.1\r\n", $request);
        $expectedHost = 'unix' === $scheme ? 'localhost' : $address;
        $this->assertStringContainsString('Host: '.$expectedHost."\r\n", $request);
    }

    public function testNegotiatesWithAnOlderDaemon(): void
    {
        $address = $this->startServer(['daemon_api_version' => '1.41']);

        $response = DockerClientFactory::create(['remote_socket' => 'tcp://'.$address, 'timeout' => 3000])->sendRequest(new Request('GET', '/containers/json'));

        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertStringStartsWith('GET /v1.41/containers/json HTTP/1.1', $this->serverResult()['request']);
    }

    public function testHttpUrlFromEnvironment(): void
    {
        $address = $this->startServer();
        putenv('DOCKER_HOST=http://'.$address);
        putenv('DOCKER_API_VERSION=1.52');

        $response = DockerClientFactory::createFromEnv()->sendRequest(new Request('GET', '/_ping'));

        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertStringStartsWith('GET /v1.52/_ping HTTP/1.1', $this->serverResult()['request']);
    }

    #[DataProvider('transports')]
    public function testExplicitApiVersionOnRealConnection(string $scheme): void
    {
        putenv('DOCKER_API_VERSION=1.52');
        $serverConfig = [];
        $clientConfig = ['api_version' => '1.45', 'timeout' => 3000];
        if ('https' === $scheme) {
            $this->createCertificates();
            $serverConfig['ssl'] = $this->serverTlsOptions();
            $clientConfig['stream_context_options']['ssl']['cafile'] = $this->directory.'/ca.pem';
        }
        $address = $this->startServer($serverConfig, 'unix' === $scheme);
        $clientConfig['remote_socket'] = $scheme.'://'.$address;
        $response = DockerClientFactory::create($clientConfig)->sendRequest(new Request('GET', '/_ping'));

        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertStringStartsWith('GET /v1.45/_ping HTTP/1.1', $this->serverResult()['request']);
        $this->assertSame('1.52', getenv('DOCKER_API_VERSION'));
    }

    public function testHttpsDoesNotAllowPlaintextDowngrade(): void
    {
        $this->createCertificates();
        $address = $this->startServer(['ssl' => $this->serverTlsOptions()]);
        $response = DockerClientFactory::create([
            'remote_socket' => 'https://'.$address,
            'ssl' => false,
            'timeout' => 3000,
            'stream_context_options' => ['ssl' => ['cafile' => $this->directory.'/ca.pem']],
        ])->sendRequest(new Request('GET', '/_ping'));

        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertNotEmpty($this->serverResult()['request']);
    }

    public function testHttpsRejectsUntrustedCertificate(): void
    {
        $this->createCertificates();
        $address = $this->startServer(['ssl' => $this->serverTlsOptions()]);
        $this->expectException(SSLConnectionException::class);

        DockerClientFactory::create([
            'remote_socket' => 'https://'.$address,
            'timeout' => 3000,
        ])->sendRequest(new Request('GET', '/_ping'));
    }

    public function testHttpsRejectsWrongPeerName(): void
    {
        $this->createCertificates();
        $address = $this->startServer(['ssl' => $this->serverTlsOptions()]);
        $this->expectException(SSLConnectionException::class);

        DockerClientFactory::create([
            'remote_socket' => 'https://'.$address,
            'timeout' => 3000,
            'stream_context_options' => ['ssl' => [
                'cafile' => $this->directory.'/ca.pem',
                'peer_name' => 'not-docker.test',
            ]],
        ])->sendRequest(new Request('GET', '/_ping'));
    }

    public static function tlsTransports(): array
    {
        return ['TCP with TLS' => ['tcp'], 'HTTPS with client certificates' => ['https']];
    }

    #[DataProvider('tlsTransports')]
    public function testTlsEnvironmentWithClientCertificates(string $scheme): void
    {
        $this->createCertificates();
        $address = $this->startServer(['ssl' => $this->serverTlsOptions() + [
            'verify_peer' => true,
            'verify_peer_name' => false,
            'cafile' => $this->directory.'/ca.pem',
            'capture_peer_cert' => true,
        ]]);
        putenv('DOCKER_HOST='.$scheme.'://'.$address);
        putenv('DOCKER_TLS_VERIFY=1');
        putenv('DOCKER_CERT_PATH='.$this->directory);
        putenv('DOCKER_PEER_NAME=docker.test');

        $response = DockerClientFactory::createFromEnv()->sendRequest(new Request('GET', '/_ping'));

        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertSame('docker-client', $this->serverResult()['peer']);
    }

    public function testUpgradedResponseRemainsReadable(): void
    {
        $address = $this->startServer(['upgrade' => true]);
        $response = DockerClientFactory::create(['remote_socket' => 'http://'.$address])->sendRequest(
            new Request('POST', '/exec/test/start', ['Connection' => 'Upgrade', 'Upgrade' => 'tcp'])
        );

        $this->assertSame(101, $response->getStatusCode());
        $this->assertSame('stream-output', $response->getBody()->getContents());
        $request = $this->serverResult()['request'];
        $this->assertStringContainsString("Connection: Upgrade\r\n", $request);
        $this->assertStringContainsString("Upgrade: tcp\r\n", $request);
    }
}
