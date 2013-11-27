<?php

namespace Docker\Tests\Manager;

use Docker\Container;
use Docker\Port;

use Docker\Tests\TestCase;

class ContainerManagerTest extends TestCase
{
    private function getManager()
    {
        return $this->getDocker()->getContainerManager();
    }

    public function testCreate()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);

        $manager = $this->getManager();
        $manager->create($container);

        $this->assertNotEmpty($container->getId());
    }

    public function testStart()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container);

        $this->assertNotEmpty($container->getId());
    }

    public function testRun()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);

        $manager = $this->getManager();
        $manager->run($container);

        $this->assertNotEmpty($container->getId());
    }

    public function testWait()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '1']]);

        $manager = $this->getManager();
        $manager->run($container);
        $manager->wait($container);

        $this->assertNotEmpty($container->getId());
    }

    public function testExposePort()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '1']]);

        $port = new Port('8888:80/tcp');

        $container->setExposedPorts($port);

        $manager = $this->getManager();
        $manager->run($container, ['PortBindings' => $port->toSpec()]);

        $this->assertEquals(8888, $container->getMappedPort(80)->getHostPort());
    }

    public function testInspect()
    {
        $manager = $this->getManager();

        $this->assertEquals(null, $manager->findById('foo'));

        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);
        $manager->create($container);

        $this->assertInstanceOf('Docker\\Container', $manager->find($container->getId()));
    }
}