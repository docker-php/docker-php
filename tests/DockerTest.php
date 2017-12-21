<?php


namespace Docker\Tests;


use Docker\API\Model\ContainerSummaryItem;
use Docker\Docker;

class DockerTest extends TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf(Docker::class, Docker::create());
    }
}