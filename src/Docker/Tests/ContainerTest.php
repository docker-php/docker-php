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

    public function testContainerName()
    {
        $container = new Container();
        $container->setName('Foobar');

        $this->assertEquals('Foobar', $container->getName());

        $container->setName('Foo-Bar');
        $this->assertEquals('Foo-Bar', $container->getName());

        $container->setName('Foo-Bar_1.2.3');
        $this->assertEquals('Foo-Bar_1.2.3', $container->getName());
    }

    public function testValidContainerName()
    {
        $container = new Container();
        $container->setName('/Foobar');
        $this->assertEquals('/Foobar', $container->getName());
    }

    public function testInvalidContainerNameOne()
    {
        $container = new Container();
        $this->setExpectedException('Exception', 'Name was not correctly formatted.');
        $container->setName('Foo/Bar/Baz');
    }

    public function testInvalidContainerNameTwo()
    {
        $container = new Container();
        $this->setExpectedException('Exception', 'Name was not correctly formatted.');
        $container->setName('Foo!');
    }

    public function testInvalidContainerNameThree()
    {
        $container = new Container();
        $this->setExpectedException('Exception', 'Name was not correctly formatted.');
        $container->setName('-FooBar');
    }

    public function testInvalidContainerNameFour()
    {
        $container = new Container();
        $this->setExpectedException('Exception', 'Name was not correctly formatted.');
        $container->setName('.FooBar');
    }

    public function testInvalidContainerNameFive()
    {
        $container = new Container();
        $this->setExpectedException('Exception', 'Name was not correctly formatted.');
        $container->setName('_FooBar');
    }

    public function testConfigImageParsing()
    {
        $container = new Container(['Image' => 'postgres:latest']);
        $image = $container->getImage();
        $this->assertEquals('postgres', $image->getRepository());
        $this->assertEquals('latest', $image->getTag());

        $container = new Container(['Image' => 'docker.io/postgres:latest']);
        $image = $container->getImage();
        $this->assertEquals('docker.io/postgres', $image->getRepository());
        $this->assertEquals('latest', $image->getTag());

        $container = new Container(['Image' => 'registry.yourcompany.com:5000/postgres:latest']);
        $image = $container->getImage();
        $this->assertEquals('registry.yourcompany.com:5000/postgres', $image->getRepository());
        $this->assertEquals('latest', $image->getTag());

    }
}
