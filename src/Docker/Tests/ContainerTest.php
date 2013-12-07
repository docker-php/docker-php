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

    public function testAddEnvWithExistingEnv()
    {
        $container = new Container(['Env' => ['FOO=BAR']]);
        $container->addEnv(['BAR=FOO']);

        $this->assertEquals(['FOO=BAR', 'BAR=FOO'], $container->getEnv());
    }
}