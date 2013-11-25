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

    public function testStartWithHostConfig()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '10']]);

        $port = new Port('80/tpc');

        $container->setExposedPorts($port);

        $manager = $this->getManager();
        $manager->run($container, ['PortBindings' => $port->toSpec()]);

        $this->assertNotEmpty($container->getId());
    }
}