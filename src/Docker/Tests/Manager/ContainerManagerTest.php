<?php

namespace Docker\Tests\Manager;

use Docker\Container;
use Docker\Port;
use Docker\Tests\TestCase;

class ContainerManagerTest extends TestCase
{
    /**
     * Return a container manager
     *
     * @return Docker\Docker\Manager\ContainerManager
     */
    private function getManager()
    {
        return $this->getDocker()->getContainerManager();
    }

    public function testFindAll()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '1']]);

        $manager = $this->getManager();
        $manager->run($container);

        $this->assertInternalType('array', $manager->findAll());
    }

    public function testCreate()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);

        $manager = $this->getManager();
        $manager->create($container);

        $this->assertNotEmpty($container->getId());
    }

    public function testCreateThrowsRightFormedException()
    {
        $container = new Container(['Image' => 'non-existent']);

        $manager = $this->getManager();

        try {
            $manager->create($container);
        } catch (\Docker\Exception\UnexpectedStatusCodeException $e) {
            $this->assertEquals('No such image: non-existent (tag: latest)', $e->getMessage());
        }
    }

    public function testStart()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container);

        $runtimeInformations = $container->getRuntimeInformations();

        $this->assertEquals(0, $runtimeInformations['State']['ExitCode']);
    }

    public function testRunDefault()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);
        $manager = $this
            ->getMockBuilder('\Docker\Manager\ContainerManager')
            ->setMethods(array('create', 'start', 'wait'))
            ->disableOriginalConstructor()
            ->getMock();

        $container->setExitCode(0);

        $manager->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $manager->expects($this->once())
            ->method('start')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $manager->expects($this->once())
            ->method('wait')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $this->assertTrue($manager->run($container));
    }

    public function testRunAttach()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);
        $manager = $this
            ->getMockBuilder('\Docker\Manager\ContainerManager')
            ->setMethods(array('create', 'start', 'wait', 'attach'))
            ->disableOriginalConstructor()
            ->getMock();

        $response = $this->getMock('\Docker\Http\Response');

        $container->setExitCode(0);
        $callback = function ($type, $output) {};

        $manager->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $manager->expects($this->once())
            ->method('attach')
            ->with($this->isInstanceOf('\Docker\Container'), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true), $this->equalTo(null))
            ->will($this->returnValue($response));

        $manager->expects($this->once())
            ->method('start')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $response->expects($this->once())
            ->method('readAttach')
            ->with($this->equalTo($callback));

        $manager->expects($this->once())
            ->method('wait')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $this->assertTrue($manager->run($container, $callback));
    }

    public function testRunDeamon()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);
        $manager = $this
            ->getMockBuilder('\Docker\Manager\ContainerManager')
            ->setMethods(array('create', 'start', 'wait'))
            ->disableOriginalConstructor()
            ->getMock();

        $container->setExitCode(0);

        $manager->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $manager->expects($this->once())
            ->method('start')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $manager->expects($this->never())
            ->method('wait');

        $this->assertNull($manager->run($container, null, array(), true));
    }

    public function testAttach()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/bash', '-c', 'echo -n "output"']]);
        $manager = $this->getManager();

        $type   = 0;
        $output = "";

        $manager->create($container);
        $response = $manager->attach($container);
        $manager->start($container);

        $response->readAttach(function ($stdtype, $log) use(&$type, &$output) {
            $type   = $stdtype;
            $output = $log;
        });

        $this->assertEquals(1, $type);
        $this->assertEquals('output', $output);
    }

    public function testAttachStderr()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/bash', '-c', 'echo -n "error" 1>&2']]);
        $manager = $this->getManager();

        $type   = 0;
        $output = "";

        $manager->create($container);
        $response = $manager->attach($container);
        $manager->start($container);

        $response->readAttach(function ($stdtype, $log) use(&$type, &$output) {
            $type   = $stdtype;
            $output = $log;
        });

        $this->assertEquals(2, $type);
        $this->assertEquals('error', $output);
    }

    /**
     * Not sure how to reliably test that we actually waited for the container
     * but this should at least ensure no exception is thrown
     */
    public function testWait()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '1']]);

        $manager = $this->getManager();
        $manager->run($container);
        $manager->wait($container);

        $runtimeInformations = $container->getRuntimeInformations();

        $this->assertEquals(0, $runtimeInformations['State']['ExitCode']);
    }

    /**
     * @expectedException Docker\Http\Exception\TimeoutException
     */
    public function testWaitWithTimeout()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '2']]);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container);
        $manager->wait($container, 1);
    }

    public function testTimeoutExceptionHasRequest()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '2']]);

        $manager = $this->getManager();
        $manager->run($container);

        try {
            $manager->wait($container, 1);
        } catch (\Docker\Http\Exception\TimeoutException $e) {
            $this->assertInstanceOf('Docker\\Http\\Request', $e->getRequest());
        }
    }

    public function testExposeFixedPort()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '1']]);

        $port = new Port('8888:80/tcp');

        $container->setExposedPorts($port);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container, ['PortBindings' => $port->toSpec()]);

        $this->assertEquals(8888, $container->getMappedPort(80)->getHostPort());
    }

    public function testExposeRandomPort()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '1']]);

        $port = new Port('80/tcp');
        $container->setExposedPorts($port);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container, ['PortBindings' => $port->toSpec()]);

        $this->assertInternalType('integer', $container->getMappedPort(80)->getHostPort());
    }

    public function testInspect()
    {
        $manager = $this->getManager();

        $this->assertEquals(null, $manager->find('foo'));

        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);
        $manager->create($container);

        $this->assertInstanceOf('Docker\\Container', $manager->find($container->getId()));
    }
}