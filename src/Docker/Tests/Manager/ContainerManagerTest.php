<?php

namespace Docker\Tests\Manager;

use Docker\Container;
use Docker\Port;
use Docker\Tests\TestCase;
use GuzzleHttp\Exception\RequestException;

class ContainerManagerTest extends TestCase
{
    /**
     * Return a container manager
     *
     * @return \Docker\Manager\ContainerManager
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

    public function testInteract()
    {
        $container = new Container([
            'Image' => 'ubuntu:precise',
            'Cmd'   => ['/bin/bash'],
            'AttachStdin'  => false,
            'AttachStdout' => true,
            'AttachStderr' => true,
            'OpenStdin'    => true,
            'Tty'          => true,
        ]);

        $manager = $this->getManager();
        $manager->create($container);
        $stream = $manager->interact($container);
        $manager->start($container);

        $this->assertNotEmpty($container->getId());
        $this->assertInstanceOf('\Docker\Http\Stream\InteractiveStream', $stream);

        stream_set_blocking($stream->getSocket(), 0);

        $read   = array($stream->getSocket());
        $write  = null;
        $expect = null;

        $stream->write("echo test\n");
        $data = "";
        do {
            $frame = $stream->receive(true);
            $data .= $frame['data'];
        } while (stream_select($read, $write, $expect, 1) > 0);

        $manager->stop($container, 1);

        $this->assertRegExp('#root@'.substr($container->getId(), 0, 12).':/\# echo test#', $data, $data);
    }

    public function testCreateThrowsRightFormedException()
    {
        $container = new Container(['Image' => 'non-existent']);

        $manager = $this->getManager();

        try {
            $manager->create($container);
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $this->assertTrue($e->hasResponse());
            $this->assertEquals("404", $e->getResponse()->getStatusCode());
            $this->assertContains('No such image: non-existent (tag: latest)', $e->getMessage());
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

        $response = $this->getMockBuilder('\GuzzleHttp\Message\Response')->disableOriginalConstructor()->getMock();
        $stream   = $this->getMockBuilder('\GuzzleHttp\Stream\Stream')->disableOriginalConstructor()->getMock();

        $container->setExitCode(0);
        $callback = function () {};

        $manager->expects($this->once())
            ->method('create')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $manager->expects($this->once())
            ->method('attach')
            ->with($this->isInstanceOf('\Docker\Container'), $this->equalTo($callback), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true), $this->equalTo(true), $this->equalTo(null))
            ->will($this->returnValue($response));

        $manager->expects($this->once())
            ->method('start')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $response->expects($this->once())
            ->method('getBody')
            ->will($this->returnValue($stream));

        $manager->expects($this->once())
            ->method('wait')
            ->with($this->isInstanceOf('\Docker\Container'))
            ->will($this->returnSelf());

        $this->assertTrue($manager->run($container, $callback));
    }

    public function testRunDaemon()
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
        $response = $manager->attach($container, function ($log, $stdtype) use (&$type, &$output) {
            $type = $stdtype;
            $output = $log;
        });
        $manager->start($container);

        $response->getBody()->getContents();

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
        $response = $manager->attach($container, function ($log, $stdtype) use (&$type, &$output) {
            $type = $stdtype;
            $output = $log;
        });
        $manager->start($container);

        $response->getBody()->getContents();

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
     * @expectedException GuzzleHttp\Exception\RequestException
     */
    public function testWaitWithTimeout()
    {
        if (getenv('DOCKER_TLS_VERIFY')) {
            $this->markTestSkipped('This test failed when using ssl due to this bug : https://bugs.php.net/bug.php?id=41631');
        }

        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '2']]);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container);
        $manager->wait($container, 1);
    }

    public function testTimeoutExceptionHasRequest()
    {
        if (getenv('DOCKER_TLS_VERIFY')) {
            $this->markTestSkipped('This test failed when using ssl due to this bug : https://bugs.php.net/bug.php?id=41631');
        }

        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '2']]);

        $manager = $this->getManager();
        $manager->run($container);

        try {
            $manager->wait($container, 1);
        } catch (RequestException $e) {
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

    public function testRemove()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['date']]);

        $manager = $this->getManager();
        $manager->create($container);
        $manager->start($container);
        $manager->wait($container);
        $manager->remove($container);

        $this->setExpectedException('\\Docker\\Exception\\ContainerNotFoundException', 'Container not found');
        $manager->inspect($container);
    }

    public function testTop()
    {
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['sleep', '2']]);
        $manager = $this->getManager();
        $manager->run($container, null, array(), true);

        $processes = $manager->top($container);

        $this->assertCount(1, $processes);
        $this->assertArrayHasKey('COMMAND', $processes[0]);
        $this->assertEquals('sleep 2', $processes[0]['COMMAND']);

        $manager->wait($container);
        $manager->remove($container);
    }
}
