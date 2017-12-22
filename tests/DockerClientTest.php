<?php


namespace Docker\Tests;


use Docker\DockerClient;
use GuzzleHttp\Psr7\Request;
use Http\Client\Common\PluginClientFactory;
use Http\Client\Socket\Client;

class DockerTestClient extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        putenv('DOCKER_TLS_VERIFY');
    }


    public function testStaticConstructor()
    {
        $this->assertInstanceOf(DockerClient::class, DockerClient::create());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Connection to docker has been set to use TLS, but no PATH is defined for certificate in DOCKER_CERT_PATH docker environnement variable
     */
    public function testCreateFromEnvWithoutCertPath()
    {
        putenv('DOCKER_TLS_VERIFY=1');
        DockerClient::createFromEnv();

    }

    public function testCreateCustomCa()
    {
        putenv('DOCKER_TLS_VERIFY=1');
        putenv('DOCKER_CERT_PATH=/tmp');

        $count = count(get_resources('stream-context'));
        $client = DockerClient::createFromEnv();
        $this->assertInstanceOf(DockerClient::class, $client);

        $contexts = get_resources('stream-context');
        $this->assertCount($count + 1, $contexts);

        // Get the last stream context.
        $context = stream_context_get_options(end($contexts));
        $this->assertSame('/tmp/ca.pem', $context['ssl']['cafile']);
        $this->assertSame('/tmp/cert.pem', $context['ssl']['local_cert']);
        $this->assertSame('/tmp/key.pem', $context['ssl']['local_pk']);
    }

    public function testCreateCustomPeerName()
    {
        putenv('DOCKER_TLS_VERIFY=1');
        putenv('DOCKER_CERT_PATH=/abc');
        putenv('DOCKER_PEER_NAME=test');

        $count = count(get_resources('stream-context'));
        $client = DockerClient::createFromEnv();
        $this->assertInstanceOf(DockerClient::class, $client);

        $contexts = get_resources('stream-context');
        $this->assertCount($count + 1, $contexts);

        // Get the last stream context.
        $context = stream_context_get_options(end($contexts));
        $this->assertSame('/abc/ca.pem', $context['ssl']['cafile']);
        $this->assertSame('/abc/cert.pem', $context['ssl']['local_cert']);
        $this->assertSame('/abc/key.pem', $context['ssl']['local_pk']);
        $this->assertSame('test', $context['ssl']['peer_name']);
    }


}