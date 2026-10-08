<?php

declare(strict_types=1);

namespace Docker\Tests\Http;

use Docker\Http\ApiVersionNegotiationPlugin;
use Http\Client\Common\PluginClient;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Request;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Client\NetworkExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ApiVersionNegotiationPluginTest extends TestCase
{
    public static function versionProvider(): iterable
    {
        yield 'older daemon' => [new Response(200, ['API-Version' => '1.43'], 'OK'), '/v1.43/containers/json'];
        yield 'newer daemon' => [new Response(200, ['API-Version' => '1.60'], 'OK'), '/v1.56/containers/json'];
        yield 'same version' => [new Response(200, ['API-Version' => '1.56'], 'OK'), '/v1.56/containers/json'];
        yield 'no version header' => [new Response(200, [], 'OK'), '/v1.56/containers/json'];
        yield 'invalid version header' => [new Response(200, ['API-Version' => 'latest'], 'OK'), '/v1.56/containers/json'];
        yield 'ping rejected' => [new Response(403, [], 'Forbidden'), '/v1.56/containers/json'];
    }

    #[DataProvider('versionProvider')]
    public function testUsesTheLowerVersionAndPingsOnce(ResponseInterface $ping, string $expectedPath): void
    {
        $transport = $this->transport(static fn (RequestInterface $request): ResponseInterface => '/_ping' === $request->getUri()->getPath() ? $ping : new Response(200, [], '[]'));
        $client = $this->client($transport);

        $client->sendRequest(new Request('GET', '/containers/json'));
        $client->sendRequest(new Request('GET', '/containers/json'));

        $this->assertSame(['/_ping', $expectedPath, $expectedPath], $transport->paths);
    }

    public function testConnectionErrorsPropagateAndAreNotRemembered(): void
    {
        $failures = 1;
        $transport = $this->transport(static function (RequestInterface $request) use (&$failures): ResponseInterface {
            if ($failures-- > 0) {
                throw new class('Connection refused', 0) extends \RuntimeException implements NetworkExceptionInterface {
                    public function getRequest(): RequestInterface
                    {
                        return new Request('GET', '/_ping');
                    }
                };
            }

            return '/_ping' === $request->getUri()->getPath() ? new Response(200, ['API-Version' => '1.44'], 'OK') : new Response(200, [], '[]');
        });
        $client = $this->client($transport);

        try {
            $client->sendRequest(new Request('GET', '/containers/json'));
            $this->fail('The ping connection error should propagate.');
        } catch (NetworkExceptionInterface) {
        }
        $client->sendRequest(new Request('GET', '/containers/json'));

        $this->assertSame(['/_ping', '/_ping', '/v1.44/containers/json'], $transport->paths);
    }

    private function client(ClientInterface $transport): PluginClient
    {
        $factory = new Psr17Factory();

        return new PluginClient($transport, [new ApiVersionNegotiationPlugin('1.56', $factory, $factory)]);
    }

    private function transport(\Closure $respond): ClientInterface
    {
        return new class($respond) implements ClientInterface {
            public array $paths = [];

            public function __construct(private \Closure $respond)
            {
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->paths[] = $request->getUri()->getPath();

                return ($this->respond)($request);
            }
        };
    }
}
