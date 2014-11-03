<?php

namespace Docker\Tests;

use Docker\Docker;
use Docker\Http\DockerClient as Client;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $docker;

    public function getDocker()
    {
        $client = new Client(array(), getenv('DOCKER_HOST') ? getenv('DOCKER_HOST') : 'unix://var/run/docker.sock');

        if (null === $this->docker) {
            $this->docker = new Docker($client);
        }

        return $this->docker;
    }
}