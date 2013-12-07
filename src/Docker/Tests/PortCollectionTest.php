<?php

namespace Docker\Tests;

use Docker\PortCollection;

use PHPUnit_Framework_TestCase;

class PortCollectionTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $ports = new PortCollection('80', '22');

        $this->assertCount(2, $ports->all());
    }

    public function testToSpec()
    {
        $ports = new PortCollection('80', '22');

        $this->assertEquals([
            '80/tcp' => [['HostIp' => '', 'HostPort' => '']],
            '22/tcp' => [['HostIp' => '', 'HostPort' => '']],
        ], $ports->toSpec());
    }

    public function testToExposedPorts()
    {
        $ports = new PortCollection('80', '22');

        $this->assertEquals([
            '80/tcp' => [],
            '22/tcp' => [],
        ], $ports->toExposedPorts());
    }
}