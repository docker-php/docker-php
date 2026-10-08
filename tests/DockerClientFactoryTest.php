<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\DockerClientFactory;
use Docker\Http\ApiVersionNegotiationPlugin;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Http\Client\Socket\Client;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Http\Client\ClientInterface;

class DockerClientFactoryTest extends TestCase
{
    private array $environment = [];

    protected function setUp(): void
    {
        parent::setUp();
        foreach (['DOCKER_HOST', 'DOCKER_API_VERSION', 'DOCKER_TLS_VERIFY', 'DOCKER_CERT_PATH', 'DOCKER_PEER_NAME'] as $name) {
            $this->environment[$name] = getenv($name);
            putenv($name);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        PluginClientFactory::setFactory(static function ($client, array $plugins, array $options): PluginClient {
            unset($options['client_name']);

            return new PluginClient($client, $plugins, $options);
        });
        foreach ($this->environment as $name => $value) {
            putenv(false === $value ? $name : $name.'='.$value);
        }
    }

    public function testStaticConstructor(): void
    {
        $this->assertInstanceOf(ClientInterface::class, DockerClientFactory::create());
    }

    public static function socketAddresses(): array
    {
        return [
            'Unix socket' => ['unix:///var/run/docker.sock', 'unix:///var/run/docker.sock', 'http://localhost', null],
            'TCP socket' => ['tcp://docker.test:2375', 'tcp://docker.test:2375', 'http://docker.test:2375', null],
            'HTTP URL' => ['http://docker.test:2375', 'tcp://docker.test:2375', 'http://docker.test:2375', null],
            'HTTPS URL' => ['https://docker.test:2376', 'tcp://docker.test:2376', 'https://docker.test:2376', true],
            'Default HTTP port' => ['http://docker.test', 'tcp://docker.test:80', 'http://docker.test', null],
            'Default HTTPS port' => ['https://docker.test', 'tcp://docker.test:443', 'https://docker.test', true],
            'Explicit default HTTPS port' => ['https://docker.test:443', 'tcp://docker.test:443', 'https://docker.test', true],
            'IPv6 HTTP' => ['http://[::1]:2375', 'tcp://[::1]:2375', 'http://[::1]:2375', null],
            'IPv6 HTTPS' => ['https://[::1]', 'tcp://[::1]:443', 'https://[::1]', true],
            'Uppercase HTTPS scheme' => ['HTTPS://docker.test:2376', 'tcp://docker.test:2376', 'https://docker.test:2376', true],
        ];
    }

    #[DataProvider('socketAddresses')]
    public function testSocketAddressAndRequestUri(string $address, string $socket, string $host, ?bool $ssl): void
    {
        $config = [];
        $uri = null;
        PluginClientFactory::setFactory(static function (Client $client, array $plugins, array $options) use (&$config, &$uri): PluginClient {
            $config = (new \ReflectionProperty($client, 'config'))->getValue($client);
            foreach ($plugins as $plugin) {
                if ($plugin instanceof \Http\Client\Common\Plugin\AddHostPlugin) {
                    $uri = (new \ReflectionProperty($plugin, 'host'))->getValue($plugin);
                }
            }
            unset($options['client_name']);

            return new PluginClient($client, $plugins, $options);
        });

        DockerClientFactory::create(['remote_socket' => $address]);

        $this->assertSame($socket, $config['remote_socket']);
        $this->assertSame($ssl, $config['ssl']);
        $this->assertSame($host, (string) $uri);
    }

    public function testNegotiatesWithoutAnExplicitVersion(): void
    {
        $plugins = $this->createdPlugins();

        $this->assertCount(1, array_filter($plugins, static fn (object $plugin): bool => $plugin instanceof ApiVersionNegotiationPlugin));
        $this->assertCount(0, array_filter($plugins, static fn (object $plugin): bool => $plugin instanceof AddPathPlugin));
    }

    public function testDefaultApiVersionMatchesGeneratedClient(): void
    {
        $specification = (new \ReflectionClass(\Docker\API\Client::class))->getFileName();
        $this->assertMatchesRegularExpression('#^1\.[0-9]+$#', DockerClientFactory::defaultApiVersion());
        $this->assertStringContainsString("createUri('/v".DockerClientFactory::defaultApiVersion()."')", file_get_contents($specification));
    }

    public function testApiVersionFromEnvironment(): void
    {
        putenv('DOCKER_API_VERSION=v1.52');

        $this->assertSame('/v1.52', $this->getApiPath());
    }

    public function testApiVersionWithLeadingSlash(): void
    {
        putenv('DOCKER_API_VERSION=/v1.52');

        $this->assertSame('/v1.52', $this->getApiPath());
    }

    public function testApiVersionWithoutVersionPrefix(): void
    {
        putenv('DOCKER_API_VERSION=1.52');

        $this->assertSame('/v1.52', $this->getApiPath());
    }

    public static function explicitApiVersions(): array
    {
        return [['1.45'], ['v1.45'], ['/v1.45'], ['/1.45']];
    }

    #[DataProvider('explicitApiVersions')]
    public function testExplicitApiVersionOverridesEnvironment(string $version): void
    {
        putenv('DOCKER_API_VERSION=1.52');

        $this->assertSame('/v1.45', $this->getApiPath(['api_version' => $version]));
        $this->assertSame('1.52', getenv('DOCKER_API_VERSION'));
    }

    public static function invalidApiVersions(): array
    {
        return [[''], [null], [1.45], [145], [false], [[]], ['1'], ['1.45/containers'], ['1.45?x=1'], [' 1.45'], ["1.45\n"]];
    }

    #[DataProvider('invalidApiVersions')]
    public function testInvalidExplicitApiVersionIsRejected($version): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('api_version must be a Docker API version');

        DockerClientFactory::create(['api_version' => $version]);
    }

    public function testApiVersionIsNotPassedToSocketClient(): void
    {
        $config = [];
        PluginClientFactory::setFactory(static function (Client $client, array $plugins, array $options) use (&$config): PluginClient {
            $config = (new \ReflectionProperty($client, 'config'))->getValue($client);
            unset($options['client_name']);

            return new PluginClient($client, $plugins, $options);
        });

        DockerClientFactory::create(['api_version' => '1.45', 'timeout' => 30000]);

        $this->assertArrayNotHasKey('api_version', $config);
        $this->assertSame(30000, $config['timeout']);
    }

    public function testCreateFromEnvWithoutCertPath(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Connection to docker has been set to use TLS, but no PATH is defined for certificate in DOCKER_CERT_PATH docker environment variable');

        putenv('DOCKER_TLS_VERIFY=1');
        DockerClientFactory::createFromEnv();
    }

    public function testCreateCustomCa(): void
    {
        putenv('DOCKER_TLS_VERIFY=1');
        putenv('DOCKER_CERT_PATH=/tmp');

        $count = \count(get_resources('stream-context'));
        $client = DockerClientFactory::createFromEnv();
        $this->assertInstanceOf(ClientInterface::class, $client);

        $contexts = get_resources('stream-context');
        $this->assertCount($count + 1, $contexts);

        // Get the last stream context.
        $context = stream_context_get_options(end($contexts));
        $this->assertSame('/tmp/ca.pem', $context['ssl']['cafile']);
        $this->assertSame('/tmp/cert.pem', $context['ssl']['local_cert']);
        $this->assertSame('/tmp/key.pem', $context['ssl']['local_pk']);
    }

    public function testCreateCustomPeerName(): void
    {
        putenv('DOCKER_TLS_VERIFY=1');
        putenv('DOCKER_CERT_PATH=/abc');
        putenv('DOCKER_PEER_NAME=test');

        $count = \count(get_resources('stream-context'));
        $client = DockerClientFactory::createFromEnv();
        $this->assertInstanceOf(ClientInterface::class, $client);

        $contexts = get_resources('stream-context');
        $this->assertCount($count + 1, $contexts);

        // Get the last stream context.
        $context = stream_context_get_options(end($contexts));
        $this->assertSame('/abc/ca.pem', $context['ssl']['cafile']);
        $this->assertSame('/abc/cert.pem', $context['ssl']['local_cert']);
        $this->assertSame('/abc/key.pem', $context['ssl']['local_pk']);
        $this->assertSame('test', $context['ssl']['peer_name']);
    }

    private function createdPlugins(array $config = []): array
    {
        $plugins = [];
        PluginClientFactory::setFactory(static function ($client, array $createdPlugins, array $options) use (&$plugins): PluginClient {
            $plugins = $createdPlugins;
            unset($options['client_name']);

            return new PluginClient($client, $createdPlugins, $options);
        });

        DockerClientFactory::create($config);

        return $plugins;
    }

    private function getApiPath(array $config = []): string
    {
        foreach ($this->createdPlugins($config) as $plugin) {
            if ($plugin instanceof AddPathPlugin) {
                $uri = (new \ReflectionProperty($plugin, 'uri'))->getValue($plugin);

                return $uri->getPath();
            }
        }

        $this->fail('The Docker API path plugin was not configured.');
    }
}
