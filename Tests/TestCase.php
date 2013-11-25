<?php

namespace Docker\Tests;

use Docker\Docker;

use Guzzle\Http\Client;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $docker;

    public function getDocker()
    {
        if (null === $this->docker) {
            $this->docker = new Docker(new Client('http://127.0.0.1:9000'));
        }

        return $this->docker;
    }
}