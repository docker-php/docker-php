<?php

namespace Docker\Tests;

use Docker\Port;

use PHPUnit_Framework_TestCase;

class PortTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provider
     */
    public function testParse($parsed, $spec, $exposed, $input)
    {
        $this->assertEquals($parsed, Port::parse($input));
    }

    /**
     * @dataProvider provider
     */
    public function testToSpec($parsed, $spec, $exposed, $input)
    {
        $port = new Port($input);
        
        $this->assertEquals($spec, $port->toSpec());
    }

    /**
     * @dataProvider provider
     */
    public function testToExposedPorts($parsed, $spec, $exposed, $input)
    {
        $port = new Port($input);

        $this->assertEquals($exposed, $port->toExposedPorts());
    }

    public function provider()
    {
        return [
            [
                ['protocol' => 'tcp', 'port' => 80, 'hostIp' => '127.0.0.1', 'hostPort' => 8080],
                ['80/tcp' => [['HostIp' => '127.0.0.1', 'HostPort' => 8080]]],
                ['80/tcp' => []],
                '127.0.0.1:8080:80/tcp'
            ],
            [
                ['protocol' => null, 'port' => 80, 'hostIp' => '127.0.0.1', 'hostPort' => 8080],
                ['80/tcp' => [['HostIp' => '127.0.0.1', 'HostPort' => 8080]]],
                ['80/tcp' => []],
                '127.0.0.1:8080:80'
            ],
            [
                ['protocol' => null, 'port' => 80, 'hostIp' => null, 'hostPort' => 8080],
                ['80/tcp' => [['HostIp' => '', 'HostPort' => 8080]]],
                ['80/tcp' => []],
                '8080:80'
            ],
        ];
    }
}