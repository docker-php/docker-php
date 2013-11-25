<?php

namespace Docker\Tests;

use Docker\Context;
use Docker\Container;

use Guzzle\Http\Client;

class DockerTest extends TestCase
{
    public function testBuild()
    {
        $context = new Context();
        $context->from('ubuntu:precise');
        $context->add('/test', 'test file content');

        $docker = $this->getDocker();
        $stream = $docker->build($context, 'foo');

        $this->assertRegExp('/Successfully built/', (string) $stream);
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

        $image = $docker->commit($container);

        $this->assertNotEmpty($image->getId());
    }

    public function testGetContainerManager()
    {
        $docker = $this->getDocker();

        $this->assertInstanceOf('Docker\\Manager\\ContainerManager', $docker->getContainerManager());
    }
}