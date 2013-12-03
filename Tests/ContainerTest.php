<?php

namespace Docker\Tests;

use Docker\Container;

use PHPUnit_Framework_TestCase;

class ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testAddEnvWithNoExistingEnv()
    {
        $container = new Container();
        $container->addEnv(['FOO=BAR']);

        $this->assertEquals(['FOO=BAR'], $container->getEnv());
    }
}