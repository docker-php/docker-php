<?php

namespace Docker\Tests;

use Docker\Docker;
use Docker\Context;
use Docker\Container;
use Docker\Context\ContextBuilder;
use Docker\Http\Client;

class DockerTest extends TestCase
{
    public function testBuild()
    {
        $contextBuilder = new ContextBuilder();
        $contextBuilder->from('ubuntu:precise');
        $contextBuilder->add('/test', 'test file content');

        $docker = $this->getDocker();
        $stream = $docker->build($contextBuilder->getContext(), 'foo');
        $stream->read();

        $this->assertRegExp('/Successfully built/', (string) $stream->getContent());
    }

    /**
     * @expectedException Docker\Http\Exception\TimeoutException
     */
    public function testGlobalHttpTimeout()
    {
        $docker = $this->getDocker();

        $docker->getHttpClient()->setTimeout(1);
        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/sleep', '2']]);

        $docker->getContainerManager()
            ->create($container)
            ->start($container)
            ->wait($container);
    }

    public function testCommit()
    {
        $container = new Container();
        $container->setImage('ubuntu:precise');
        $container->setCmd(['/bin/true']);

        $docker = $this->getDocker();
        $manager = $docker->getContainerManager();

        $manager->run($container);
        $manager->wait($container);

        $image = $docker->commit($container, ['repo' => 'test', 'tag' => 'foo']);

        $this->assertNotEmpty($image->getId());
        $this->assertEquals('test', $image->getRepository());
        $this->assertEquals('foo', $image->getTag());
    }

    public function testGetContainerManager()
    {
        $docker = $this->getDocker();

        $this->assertInstanceOf('Docker\\Manager\\ContainerManager', $docker->getContainerManager());
    }
}