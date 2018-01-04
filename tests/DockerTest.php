<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\Docker;

class DockerTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertInstanceOf(Docker::class, Docker::create());
    }
}
