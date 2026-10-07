<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\API\Model\ExecIdStartPostBody;
use Docker\Docker;
use Docker\DockerClientFactory;
use PHPUnit\Framework\Attributes\DataProvider;

class InteractiveExecTransportTest extends TransportTestCase
{
    public static function transports(): iterable
    {
        foreach (['unix', 'tcp', 'https'] as $scheme) {
            foreach ([false, true] as $tty) {
                yield $scheme.' tty='.(int) $tty => [$scheme, $tty];
            }
        }
    }

    #[DataProvider('transports')]
    public function testBidirectionalOutputAndHalfClose(string $scheme, bool $tty): void
    {
        $serverConfig = ['tty' => $tty, 'input_length' => 9 * 16384];
        $config = ['timeout' => 3000, 'api_version' => '1.45'];
        if ('https' === $scheme) {
            $this->createCertificates();
            $serverConfig['ssl'] = $this->serverTlsOptions();
            $config['stream_context_options']['ssl']['cafile'] = $this->directory.'/ca.pem';
        }
        $address = $this->startServer($serverConfig, 'unix' === $scheme, 'interactive-server.php');
        $config['remote_socket'] = $scheme.'://'.$address;
        putenv('DOCKER_API_VERSION=1.52');
        $docker = Docker::create(DockerClientFactory::createInteractive($config), [], [], false);
        $body = new ExecIdStartPostBody();
        $body->setTty($tty);
        $session = $docker->execStartInteractive('test', $body, 5000);
        $payload = str_repeat("binary\0\xff\n", 16384);
        $stdout = $stderr = '';
        $session->onStdout(static function (string $chunk) use (&$stdout): void { $stdout .= $chunk; });
        $session->onStderr(static function (string $chunk) use (&$stderr): void { $stderr .= $chunk; });
        self::assertTrue($session->poll(100));
        self::assertSame('ready:', $stdout, 'Buffered bytes sent with the HTTP headers were lost.');
        $offset = 0;
        while ($offset < \strlen($payload)) {
            $offset += $session->writeStdin(substr($payload, $offset));
            $session->poll(1);
        }
        if (!$tty) {
            $session->closeStdin();
        }
        $session->wait();
        self::assertSame('ready:'.$payload.':done'.($tty ? 'error' : ''), $stdout);
        self::assertSame($tty ? '' : 'error', $stderr);
        $request = $this->serverResult(1);
        self::assertSame(\strlen($payload), $request['stdin_length']);
        self::assertSame(hash('sha256', $payload), $request['stdin_hash']);
        self::assertStringStartsWith('POST /v1.45/exec/test/start HTTP/1.1', $request['request']);
        self::assertStringContainsString("Upgrade: tcp\r\n", $request['request']);
        self::assertSame(['Detach' => false, 'Tty' => $tty], json_decode($request['body'], true));
        self::assertNull($body->getDetach(), 'The caller model was mutated.');
    }

    public function testCustomHttpClientFailsBeforeStartingExec(): void
    {
        $http = new class() implements \Psr\Http\Client\ClientInterface {
            public int $requests = 0;

            public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
            {
                ++$this->requests;

                return new \Nyholm\Psr7\Response(200, ['Content-Type' => 'application/json'], '{}');
            }
        };
        $docker = Docker::create($http, [], [], false);
        try {
            $docker->execStartInteractive('test');
            self::fail('Unsupported transport was accepted.');
        } catch (\LogicException $expected) {
            self::assertSame(1, $http->requests);
        }
    }

    public function testDetachedExecIsRejectedBeforeTheRequest(): void
    {
        $address = $this->startServer(['responses' => [['body' => '{}']]], true);
        $docker = Docker::create(DockerClientFactory::createInteractive(['remote_socket' => 'unix://'.$address]), [], [], false);
        $body = new ExecIdStartPostBody();
        $body->setDetach(true);
        $this->expectException(\InvalidArgumentException::class);
        $docker->execStartInteractive('test', $body);
    }

    public function testARegularHttpResponseIsNotTreatedAsAnInteractiveSession(): void
    {
        $address = $this->startServer(['responses' => [
            ['body' => '{}'],
            ['status' => 409, 'body' => '{"message":"fixture conflict"}', 'headers' => ['Content-Type' => 'application/json']],
        ]], true);
        $docker = Docker::create(DockerClientFactory::createInteractive(['remote_socket' => 'unix://'.$address]), [], [], false);
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('HTTP 409');
        $docker->execStartInteractive('test');
    }
}
