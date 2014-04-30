<?php

namespace Docker\Tests;

use Docker\Docker;
use GuzzleHttp\Client;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $docker;

    public function getDocker()
    {
        $client = new Client(array('base_url' => 'http://127.0.0.1:4243'));
        $client->setDefaultOption('timeout' , 5);

        if (null === $this->docker) {
            $this->docker = new Docker($client);
        }

        return $this->docker;
    }
}