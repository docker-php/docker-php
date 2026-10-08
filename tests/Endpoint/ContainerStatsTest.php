<?php

declare(strict_types=1);

namespace Docker\Tests\Endpoint;

use Docker\API\Exception\ContainerStatsNotFoundException;
use Docker\API\Model\ErrorResponse;
use Docker\API\Normalizer\JaneObjectNormalizer;
use Docker\Endpoint\ContainerStats;
use Docker\Stream\StatsStream;
use Nyholm\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;
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

        $stats = (new ContainerStats('web', ['stream' => false]))->parseResponse($response, self::serializer());

        $this->assertSame(1, self::memoryUsage($stats));
    }

    public function testStreamedSamplesMatchTheSingleSampleType(): void
    {
        $response = new Response(200, self::JSON, '{"read":"t1","memory_stats":{"usage":1}}{"read":"t2","memory_stats":{"usage":2}}');
        $single = (new ContainerStats('web', ['stream' => false]))->parseResponse(new Response(200, self::JSON, '{"read":"t0","memory_stats":{"usage":0}}'), self::serializer());
        $stream = (new ContainerStats('web'))->parseResponse($response, self::serializer());
        $samples = [];
        $stream->onFrame(static function (object $sample) use (&$samples): void {
            $samples[] = $sample;
        });
        $stream->wait();

        $this->assertSame([1, 2], array_map(self::memoryUsage(...), $samples));
        $this->assertSame($single::class, $samples[0]::class);
        // API 1.48 added a generated stats model; earlier versions decode to stdClass.
        $this->assertSame(class_exists('Docker\\API\\Model\\ContainerStatsResponse') ? 'Docker\\API\\Model\\ContainerStatsResponse' : \stdClass::class, $single::class);
    }

    private static function serializer(): Serializer
    {
        return new Serializer(
            [new ArrayDenormalizer(), new JaneObjectNormalizer()],
            [new JsonEncoder(new JsonEncode(), new JsonDecode(['json_decode_associative' => true]))]
        );
    }

    private static function memoryUsage(object $sample): int
    {
        return $sample instanceof \stdClass ? $sample->memory_stats->usage : $sample->getMemoryStats()->getUsage();
    }

    public function testMissingContainerStillThrows(): void
    {
        $serializer = $this->createMock(SerializerInterface::class);
        $serializer->method('deserialize')->willReturn(new ErrorResponse());
        $this->expectException(ContainerStatsNotFoundException::class);

        (new ContainerStats('missing'))->parseResponse(new Response(404, self::JSON, '{"message":"No such container"}'), $serializer);
    }
}
