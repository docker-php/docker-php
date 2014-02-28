<?php

namespace Docker\Tests;

use Docker\Docker;
use Docker\Http\Client;

use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $docker;

    public function getDocker()
    {
        if (null === $this->docker) {
            $this->docker = new Docker(new Client('tcp://127.0.0.1:4243'));
        }

        return $this->docker;
    }
}