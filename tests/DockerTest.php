<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\Docker;
use Docker\Exception\BadRequestException;
use Nyholm\Psr7\Response;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class DockerTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertInstanceOf(Docker::class, Docker::create());
    }

    public function testCreateForwardsServerPluginOption(): void
    {
        $httpClient = new class implements ClientInterface {
            public ?RequestInterface $request = null;

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $this->request = $request;

                return new Response(200, ['Content-Type' => 'application/json'], '{}');
            }
        };

        $this->assertInstanceOf(Docker::class, Docker::create($httpClient, [], [], false));
        $this->assertSame('/info', $httpClient->request?->getUri()->getPath());
    }

    public function testCreateThrowsConcreteExceptionForInvalidResponse(): void
    {
        $httpClient = new class implements ClientInterface {
            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                return new Response(200, ['Content-Type' => 'application/json'], 'invalid');
            }
        };

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Failed to decode JSON.');

        Docker::create($httpClient, [], [], false);
    }
}
