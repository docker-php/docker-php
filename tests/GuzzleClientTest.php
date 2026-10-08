<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\API\Exception\ContainerInspectNotFoundException;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\Docker;
use Docker\DockerClientFactory;
use Docker\Stream\DockerRawStream;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\InvalidArgumentException as GuzzleInvalidArgumentException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\TransferStats;
use Http\Client\Common\PluginClient;
use Nyholm\Psr7\Request;
use PHPUnit\Framework\Attributes\DataProvider;

class GuzzleClientTest extends TransportTestCase
{
    public static function connectionExamples(): array
    {
        return [
            'Custom Unix socket' => ['Guzzle Unix socket', 'unix'],
            'HTTP endpoint' => ['Guzzle Unix socket', 'http'],
            'Private CA' => ['Guzzle HTTPS', 'https'],
            'Mutual TLS' => ['Guzzle HTTPS', 'mutual-tls'],
        ];
    }

    #[DataProvider('connectionExamples')]
    public function testDocumentationExampleUsesConfiguredConnection(string $example, string $transport): void
    {
        $serverOptions = ['responses' => [$this->jsonResponse('{}'), $this->jsonResponse('[]')]];
        if ('https' === $transport || 'mutual-tls' === $transport) {
            $this->createCertificates();
            $serverOptions['ssl'] = $this->serverTlsOptions();
        }
        if ('mutual-tls' === $transport) {
            $serverOptions['ssl'] += [
                'verify_peer' => true,
                'verify_peer_name' => false,
                'cafile' => $this->directory.'/ca.pem',
                'capture_peer_cert' => true,
            ];
        }
        $address = $this->startServer($serverOptions, 'unix' === $transport);

        // A supplied client must not read the bundled factory's connection settings.
        putenv('DOCKER_HOST=unix:///does-not-exist.sock');
        putenv('DOCKER_TLS_VERIFY=1');
        putenv('DOCKER_API_VERSION=v0.0');
        $docker = $this->runDocumentationExample($example, $transport, $address);

        $this->assertSame([], $docker->containerList());
        $this->assertStringStartsWith('GET /v'.DockerClientFactory::defaultApiVersion().'/info HTTP/1.1', $this->serverResult()['request']);
        $request = $this->serverResult(1);
        $this->assertStringStartsWith('GET /v'.DockerClientFactory::defaultApiVersion().'/containers/json?', $request['request']);
        $host = 'unix' === $transport ? 'localhost' : $address;
        $this->assertStringContainsString('Host: '.$host."\r\n", $request['request']);
        if ('mutual-tls' === $transport) {
            $this->assertSame('docker-client', $request['peer']);
        }
    }

    public function testHttpsRejectsUntrustedCertificate(): void
    {
        $this->createCertificates();
        $address = $this->startServer(['ssl' => $this->serverTlsOptions()]);

        $this->assertCertificateRejected('https://'.$address);
    }

    public function testHttpsRejectsWrongHostname(): void
    {
        $this->createCertificates('DNS:not-docker.test');
        $address = $this->startServer(['ssl' => $this->serverTlsOptions()]);

        $this->assertCertificateRejected('https://'.$address, ['verify' => $this->directory.'/ca.pem']);
    }

    public function testMutualTlsRejectsMissingClientCertificate(): void
    {
        $this->createCertificates();
        $address = $this->startServer(['ssl' => $this->serverTlsOptions() + [
            'verify_peer' => true,
            'verify_peer_name' => false,
            'cafile' => $this->directory.'/ca.pem',
        ]]);
        $this->expectException(GuzzleException::class);

        Docker::create($this->guzzle('https://'.$address, ['verify' => $this->directory.'/ca.pem']));
    }

    public function testRequestBodyReachesDaemon(): void
    {
        $address = $this->startServer(['responses' => [
            $this->jsonResponse('{}'),
            $this->jsonResponse('{"Id":"created","Warnings":[]}', 201),
        ]]);
        $docker = Docker::create($this->guzzle('http://'.$address));
        $body = new ContainersCreatePostBody();
        $body->setImage('busybox:latest');
        $body->setEnv(['MODE=test']);

        $this->assertSame('created', $docker->containerCreate($body)->getId());
        $request = $this->serverResult(1);
        [$method, $uri] = explode(' ', $request['request'], 3);
        $this->assertSame('POST', $method);
        $this->assertSame('/v'.DockerClientFactory::defaultApiVersion().'/containers/create', parse_url($uri, \PHP_URL_PATH));
        $json = json_decode($request['body'], true, 512, \JSON_THROW_ON_ERROR);
        $this->assertSame('busybox:latest', $json['Image']);
        $this->assertSame(['MODE=test'], $json['Env']);
    }

    public function testApiErrorUsesGeneratedException(): void
    {
        $address = $this->startServer(['responses' => [
            $this->jsonResponse('{}'),
            $this->jsonResponse('{"message":"No such container: missing"}', 404),
        ]]);
        $docker = Docker::create($this->guzzle('http://'.$address));
        $this->expectException(ContainerInspectNotFoundException::class);

        $docker->containerInspect('missing');
    }

    public function testCompletedLogResponseDecodesStdoutAndStderr(): void
    {
        $frames = pack('CxxxN', 1, 6)."hello\n".pack('CxxxN', 2, 5)."warn\n";
        $address = $this->startServer(['responses' => [
            $this->jsonResponse('{}'),
            ['body_base64' => base64_encode($frames), 'headers' => ['Content-Type' => DockerRawStream::MULTIPLEXED_HEADER]],
        ]]);
        $docker = Docker::create($this->guzzle('http://'.$address));
        $logs = $docker->containerLogs('test', ['stdout' => true, 'stderr' => true]);
        $stdout = '';
        $stderr = '';
        $logs->onStdout(static function (string $chunk) use (&$stdout): void {
            $stdout .= $chunk;
        });
        $logs->onStderr(static function (string $chunk) use (&$stderr): void {
            $stderr .= $chunk;
        });
        $logs->wait();

        $this->assertSame("hello\n", $stdout);
        $this->assertSame("warn\n", $stderr);
        $this->assertStringContainsString('/v'.DockerClientFactory::defaultApiVersion().'/containers/test/logs?', $this->serverResult(1)['request']);
    }

    public function testExplicitCurlHandlerBuffersDespiteStreamOption(): void
    {
        $address = $this->startServer(['negotiation' => false]);
        $client = new PluginClient($this->guzzle('http://'.$address, ['stream' => true]));
        $response = $client->sendRequest(new Request('GET', '/_ping'));

        $this->assertTrue($response->getBody()->isSeekable());
        $this->assertSame('php://temp', $response->getBody()->getMetadata('uri'));
        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertStringStartsWith('GET /_ping HTTP/1.1', $this->serverResult()['request']);
    }

    public function testDefaultStreamingHandlerReadsIncrementally(): void
    {
        if (!\ini_get('allow_url_fopen')) {
            $this->markTestSkipped('The default PHP stream handler requires allow_url_fopen.');
        }
        $address = $this->startServer(['negotiation' => false]);
        $client = new PluginClient(new GuzzleClient([
            'base_uri' => 'http://'.$address,
            'stream' => true,
            'proxy' => '',
            'timeout' => 3,
        ]));
        $response = $client->sendRequest(new Request('GET', '/_ping'));

        $this->assertFalse($response->getBody()->isSeekable());
        $this->assertSame('http', $response->getBody()->getMetadata('wrapper_type'));
        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertStringContainsString('Host: '.$address."\r\n", $this->serverResult()['request']);
    }

    public function testDefaultStreamingHandlerDoesNotUseCurlSocketOption(): void
    {
        if (!\ini_get('allow_url_fopen')) {
            $this->markTestSkipped('The default PHP stream handler requires allow_url_fopen.');
        }
        $options = [
            'stream' => true,
            'curl' => [\CURLOPT_UNIX_SOCKET_PATH => $this->directory.'/does-not-exist.sock'],
            'proxy' => '',
            'timeout' => 3,
        ];

        if (ClientInterface::MAJOR_VERSION >= 8) {
            // Guzzle 8 rejects cURL-only options on the stream handler.
            $client = new PluginClient(new GuzzleClient($options + ['base_uri' => 'http://localhost']));
            $this->expectException(GuzzleInvalidArgumentException::class);
            $client->sendRequest(new Request('GET', '/_ping'));

            return;
        }

        // Guzzle 7 ignores them and connects to base_uri instead of the socket.
        $address = $this->startServer(['negotiation' => false]);
        $client = new PluginClient(new GuzzleClient($options + ['base_uri' => 'http://'.$address]));
        $response = $client->sendRequest(new Request('GET', '/_ping'));

        $this->assertSame('OK', $response->getBody()->getContents());
        $this->assertStringContainsString('Host: '.$address."\r\n", $this->serverResult()['request']);
    }

    private function guzzle(string $url, array $options = []): GuzzleClient
    {
        return new GuzzleClient($options + [
            'base_uri' => $url,
            'handler' => HandlerStack::create(new CurlHandler()),
            'proxy' => '',
            'timeout' => 3,
        ]);
    }

    private function jsonResponse(string $body, int $status = 200): array
    {
        return ['status' => $status, 'body' => $body, 'headers' => ['Content-Type' => 'application/json']];
    }

    private function assertCertificateRejected(string $url, array $options = []): void
    {
        // Guzzle 8 removed the exception handler context; transfer stats report the cURL errno in both majors.
        $errno = null;
        $client = $this->guzzle($url, $options + [
            'on_stats' => static function (TransferStats $stats) use (&$errno): void {
                $errno = $stats->getHandlerErrorData();
            },
        ]);

        try {
            Docker::create($client);
            $this->fail('The server certificate should have been rejected.');
        } catch (GuzzleException) {
            $this->assertSame(\CURLE_SSL_CACERT, $errno);
        }
    }

    private function runDocumentationExample(string $title, string $transport, string $address): Docker
    {
        $markdown = file_get_contents(\dirname(__DIR__).'/docs/guides/guzzle.mdx');
        preg_match_all('/^```php ([^\r\n]+)\R(.*?)^```/ms', $markdown, $blocks);
        $examples = array_combine($blocks[1], $blocks[2]);
        $this->assertArrayHasKey($title, $examples);
        $code = preg_replace('/^\s*<\?php\s*/', '', $examples[$title]);
        $code = str_replace("require __DIR__ . '/vendor/autoload.php';", '', $code);
        // Exercise the HTTP and CA-only variations described below the examples.
        if ('http' === $transport) {
            $code = preg_replace("/    'curl' => \\[\\R.*?    \\],\\R/s", '', $code);
            $code = str_replace("'http://localhost'", "'http://docker.example.com:2375'", $code);
        }
        if ('https' === $transport) {
            $code = preg_replace("/^    '(cert|ssl_key)' => [^\\r\\n]+\\R/m", '', $code);
        }
        $code = strtr($code, [
            '/run/custom/docker.sock' => $address,
            'http://docker.example.com:2375' => 'http://'.$address,
            'https://docker.example.com:2376' => 'https://'.$address,
            '/path/to/ca.pem' => $this->directory.'/ca.pem',
            '/path/to/cert.pem' => $this->directory.'/cert.pem',
            '/path/to/key.pem' => $this->directory.'/key.pem',
        ]);

        return (static fn (string $code): Docker => eval("namespace {\n".$code."\nreturn \$docker;\n}"))($code);
    }
}
