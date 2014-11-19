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
        $client = Client::createWithEnv();

        if (null === $this->docker) {
            $this->docker = new Docker($client);
        }

        return $this->docker;
    }
}
