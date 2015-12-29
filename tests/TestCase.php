<?php

namespace Docker\Tests;

use Docker\Docker;
use PHPUnit_Framework_TestCase;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $docker;

    public function getDocker()
    {
        if (null === $this->docker) {
            $this->docker = new Docker();
        }

        return $this->docker;
    }
}
