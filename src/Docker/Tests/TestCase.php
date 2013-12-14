<?php

namespace Docker\Tests;

use Docker\Docker;
use Zend\Http\Client;
use PHPUnit_Framework_TestCase;
use Docker\Http\Adapter\UnixSocket;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $docker;

    public function getDocker()
    {
        if (null === $this->docker) {
            $client  = new Client('http://localhost');
            $adapter = new UnixSocket();
            $adapter->setEntryPoint("unix:///var/run/docker.sock");
            $client->setAdapter($adapter);

            $this->docker = new Docker($client);
        }

        return $this->docker;
    }
}