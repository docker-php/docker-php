<?php

declare(strict_types=1);

namespace Docker\Tests;

use Composer\InstalledVersions;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\API\Model\ContainersIdJsonGetResponse200;
use Docker\API\Model\HealthcheckResult;
use Docker\API\Model\NetworkSettings;
use Docker\API\Model\PortBinding;
use Docker\API\Normalizer\JaneObjectNormalizer;
use Docker\Docker;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Serializer;

class GeneratedNormalizerTest extends TestCase
{
    private function serializer(): Serializer
    {
        return new Serializer(
            [new ArrayDenormalizer(), new JaneObjectNormalizer()],
            [new JsonEncoder(null, new JsonDecode(['json_decode_associative' => true]))]
        );
    }

    public function testRenamedPackagesAreInstalledTogether(): void
    {
        self::assertTrue(InstalledVersions::isInstalled('docker-php/docker-php'));
        self::assertTrue(InstalledVersions::isInstalled('docker-php/docker-php-api'));
        self::assertFalse(InstalledVersions::isInstalled('beluga-php/docker-php'));
        self::assertFalse(InstalledVersions::isInstalled('beluga-php/docker-php-api'));
        foreach ([Docker::class => 'docker-php/docker-php', NetworkSettings::class => 'docker-php/docker-php-api'] as $class => $package) {
            $path = realpath(InstalledVersions::getInstallPath($package));
            self::assertStringStartsWith($path.\DIRECTORY_SEPARATOR, (new \ReflectionClass($class))->getFileName());
        }
    }

    public static function timestampProvider(): iterable
    {
        yield 'nanoseconds UTC' => ['2019-12-22T10:59:05.638593300Z', '2019-12-22T10:59:05.638593+00:00'];
        yield 'localstack seven fractional digits' => ['2019-12-22T10:59:05.6385933Z', '2019-12-22T10:59:05.638593+00:00'];
        yield 'microseconds with offset' => ['2019-12-22T10:59:05.638593+02:00', '2019-12-22T10:59:05.638593+02:00'];
        yield 'seconds only' => ['2019-12-22T10:59:05Z', '2019-12-22T10:59:05.000000+00:00'];
    }

    /** @dataProvider timestampProvider */
    public function testHealthcheckTimestamp(string $input, string $expected): void
    {
        $result = $this->serializer()->deserialize(json_encode(['Start' => $input], \JSON_THROW_ON_ERROR), HealthcheckResult::class, 'json');
        self::assertInstanceOf(\DateTimeInterface::class, $result->getStart());
        self::assertSame($expected, $result->getStart()->format('Y-m-d\TH:i:s.uP'));
    }

    public function testMissingAndNullHealthcheckTimestamp(): void
    {
        $serializer = $this->serializer();
        self::assertFalse($serializer->deserialize('{}', HealthcheckResult::class, 'json')->isInitialized('start'));
        $result = $serializer->deserialize('{"Start":null}', HealthcheckResult::class, 'json');
        self::assertTrue($result->isInitialized('start'));
        self::assertNull($result->getStart());
    }

    public static function emptyObjectProvider(): iterable
    {
        yield 'array' => [[]];
        yield 'ArrayObject' => [new \ArrayObject()];
        yield 'stdClass' => [new \stdClass()];
    }

    /** @dataProvider emptyObjectProvider */
    public function testEmptyExposedPortsAndVolumesAreObjects(mixed $empty): void
    {
        $body = new ContainersCreatePostBody();
        $body->setImage('busybox:latest');
        $body->setExposedPorts(['80/tcp' => $empty, '53/udp' => $empty]);
        $body->setVolumes(['/data' => $empty]);
        $json = json_decode($this->serializer()->serialize($body, 'json'), false, 512, \JSON_THROW_ON_ERROR);
        self::assertInstanceOf(\stdClass::class, $json->ExposedPorts->{'80/tcp'});
        self::assertInstanceOf(\stdClass::class, $json->ExposedPorts->{'53/udp'});
        self::assertInstanceOf(\stdClass::class, $json->Volumes->{'/data'});
    }

    public function testNullPortBindingsRoundTripThroughContainerInspect(): void
    {
        $payload = '{"Id":"example","NetworkSettings":{"Ports":{"80/tcp":[{"HostIp":"127.0.0.1","HostPort":"8080"}],"2377/tcp":null,"53/udp":[]}}}';
        $serializer = $this->serializer();
        $container = $serializer->deserialize($payload, ContainersIdJsonGetResponse200::class, 'json');
        $ports = $container->getNetworkSettings()->getPorts();
        self::assertNull($ports['2377/tcp']);
        self::assertSame([], $ports['53/udp']);
        self::assertInstanceOf(PortBinding::class, $ports['80/tcp'][0]);
        self::assertSame('8080', $ports['80/tcp'][0]->getHostPort());
        self::assertSame('127.0.0.1', $ports['80/tcp'][0]->getHostIp());
        self::assertSame(json_decode($payload, true), json_decode($serializer->serialize($container, 'json'), true));
    }

    public function testEmptyPortMapRemainsAnObject(): void
    {
        $serializer = $this->serializer();
        $settings = $serializer->deserialize('{"Ports":{}}', NetworkSettings::class, 'json');
        self::assertSame('{"Ports":{}}', $serializer->serialize($settings, 'json'));
    }
}
