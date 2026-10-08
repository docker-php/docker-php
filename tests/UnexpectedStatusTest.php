<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\API\Exception\ClientException;
use Docker\API\Exception\ContainerInspectNotFoundException;
use Docker\API\Model\ContainersIdUpdatePostBody;
use Docker\Docker;
use Docker\Exception\UnexpectedClientErrorException;
use Docker\Exception\UnexpectedServerErrorException;
use Docker\Stream\DockerRawStream;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UnexpectedStatusTest extends TestCase
{
    private const JSON = ['Content-Type' => 'application/json'];

    private array $deprecations = [];

    protected function setUp(): void
    {
        set_error_handler(function (int $level, string $message): bool {
            $this->deprecations[] = $message;

            return true;
        }, \E_USER_DEPRECATED);
    }

    protected function tearDown(): void
    {
        restore_error_handler();
    }

    public function testUnhandledErrorReturnsNullWithDeprecationByDefault(): void
    {
        $docker = $this->docker(new Response(400, self::JSON, '{"message":"Minimum memory limit allowed is 6MB"}'));

        $this->assertNull($docker->containerUpdate('web', new ContainersIdUpdatePostBody()));
        $this->assertCount(1, $this->deprecations);
        $this->assertStringContainsString('Docker returned HTTP 400 for POST /containers/web/update: Minimum memory limit allowed is 6MB', $this->deprecations[0]);
        $this->assertStringContainsString('throwOnUnexpectedStatus()', $this->deprecations[0]);
    }

    public function testOptInThrowsClientError(): void
    {
        $docker = $this->docker(new Response(409, self::JSON, '{"message":"network with name web already exists"}'))->throwOnUnexpectedStatus();

        try {
            $docker->containerUpdate('web', new ContainersIdUpdatePostBody());
            $this->fail('An unhandled 409 should throw.');
        } catch (UnexpectedClientErrorException $error) {
            $this->assertInstanceOf(ClientException::class, $error);
            $this->assertSame(409, $error->getStatusCode());
            $this->assertSame(409, $error->getCode());
            $this->assertSame('POST', $error->getMethod());
            $this->assertSame('/containers/web/update', $error->getPath());
            $this->assertSame('network with name web already exists', $error->getErrorMessage());
            $this->assertSame('{"message":"network with name web already exists"}', (string) $error->getResponse()->getBody());
        }
        $this->assertSame([], $this->deprecations);
    }

    public function testOptInThrowsServerErrorWithPlainTextBody(): void
    {
        $docker = $this->docker(new Response(500, ['Content-Type' => 'text/plain'], "invalid reference format\n"))->throwOnUnexpectedStatus();

        $this->expectException(UnexpectedServerErrorException::class);
        $this->expectExceptionMessage('Docker returned HTTP 500 for POST /images/create: invalid reference format');
        $docker->imageCreate(null, ['fromImage' => 'Bad Name']);
    }

    public function testDocumentedErrorsKeepGeneratedExceptions(): void
    {
        $docker = $this->docker(new Response(404, self::JSON, '{"message":"No such container: web"}'));

        try {
            $docker->containerInspect('web');
            $this->fail('A documented 404 should throw the generated exception.');
        } catch (ContainerInspectNotFoundException) {
        }
        $this->assertSame([], $this->deprecations);
    }

    public function testSuccessWithoutBodyStillReturnsNull(): void
    {
        $docker = $this->docker(new Response(304))->throwOnUnexpectedStatus();

        $this->assertNull($docker->containerStart('web'));
        $this->assertSame([], $this->deprecations);
    }

    public function testRawResponsesAreNotChecked(): void
    {
        $docker = $this->docker(new Response(400, self::JSON, '{"message":"bad"}'))->throwOnUnexpectedStatus();

        $response = $docker->containerUpdate('web', new ContainersIdUpdatePostBody(), Docker::FETCH_RESPONSE);

        $this->assertSame(400, $response->getStatusCode());
    }

    public function testSuccessfulStreamsAreNotBuffered(): void
    {
        $response = new Response(200, ['Content-Type' => DockerRawStream::MULTIPLEXED_HEADER], pack('CxxxN', 1, 5).'hello');
        $docker = $this->docker($response)->throwOnUnexpectedStatus();

        $stream = $docker->containerLogs('web', ['stdout' => true]);

        $this->assertInstanceOf(DockerRawStream::class, $stream);
        $this->assertSame(0, $response->getBody()->tell(), 'Successful responses must stay unread');
    }

    private function docker(ResponseInterface ...$responses): Docker
    {
        $httpClient = new class([new Response(200, self::JSON, '{}'), ...$responses]) implements ClientInterface {
            public function __construct(private array $responses)
            {
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return array_shift($this->responses) ?? throw new \LogicException('Unexpected request '.$request->getUri());
            }
        };

        return Docker::create($httpClient, [], [], false);
    }
}
