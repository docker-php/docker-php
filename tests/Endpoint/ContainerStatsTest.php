<?php

declare(strict_types=1);

namespace Docker\Tests\Endpoint;

use Docker\API\Exception\ContainerStatsNotFoundException;
use Docker\API\Model\ErrorResponse;
use Docker\Endpoint\ContainerStats;
use Docker\Stream\StatsStream;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\SerializerInterface;

class ContainerStatsTest extends TestCase
{
    private const JSON = ['Content-Type' => 'application/json'];

    public function testStreamsByDefaultWithoutReadingBody(): void
    {
        $response = new Response(200, self::JSON, "{\"read\":\"t1\"}\n{\"read\":\"t2\"}\n");

        $stats = (new ContainerStats('web'))->parseResponse($response, $this->createMock(SerializerInterface::class));

        $this->assertInstanceOf(StatsStream::class, $stats);
        $this->assertSame(0, $response->getBody()->tell(), 'Creating the wrapper must not consume samples');
    }

    public function testReturnsOneSampleWhenStreamingIsOff(): void
    {
        $response = new Response(200, self::JSON, '{"read":"t1","memory_stats":{"usage":1}}');

        $stats = (new ContainerStats('web', ['stream' => false]))->parseResponse($response, $this->createMock(SerializerInterface::class));

        $this->assertInstanceOf(\stdClass::class, $stats);
        $this->assertSame(1, $stats->memory_stats->usage);
    }

    public function testMissingContainerStillThrows(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')->willReturn(new ErrorResponse());
        $this->expectException(ContainerStatsNotFoundException::class);

        (new ContainerStats('missing'))->parseResponse(new Response(404, self::JSON, '{"message":"No such container"}'), $serializer);
    }
}
