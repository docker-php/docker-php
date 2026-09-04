<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\DockerClientFactory;
use Http\Client\Common\Plugin\AddPathPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Common\PluginClientFactory;
use Psr\Http\Client\ClientInterface;

class DockerClientFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        PluginClientFactory::setFactory(static function ($client, array $plugins, array $options): PluginClient {
            unset($options['client_name']);

            return new PluginClient($client, $plugins, $options);
        });
        putenv('DOCKER_API_VERSION');
        putenv('DOCKER_TLS_VERIFY');
    }

    public function testStaticConstructor(): void
    {
        $this->assertInstanceOf(ClientInterface::class, DockerClientFactory::create());
    }

    public function testDefaultApiVersion(): void
    {
        $this->assertSame('/v1.45', $this->getApiPath());
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

    private function getApiPath(): string
    {
        $plugins = [];
        PluginClientFactory::setFactory(static function ($client, array $createdPlugins, array $options) use (&$plugins): PluginClient {
            $plugins = $createdPlugins;
            unset($options['client_name']);

            return new PluginClient($client, $createdPlugins, $options);
        });

        DockerClientFactory::create();

        foreach ($plugins as $plugin) {
            if ($plugin instanceof AddPathPlugin) {
                $uri = (new \ReflectionProperty($plugin, 'uri'))->getValue($plugin);

                return $uri->getPath();
            }
        }

        $this->fail('The Docker API path plugin was not configured.');
    }
}
