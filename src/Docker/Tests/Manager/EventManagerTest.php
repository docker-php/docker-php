<?php

namespace Docker\Tests\Manager;

use Docker\Container;
use Docker\Tests\TestCase;
use PHPUnit_Framework_Constraint_Callback;

class EventManagerTest extends TestCase
{

    public function testCreate()
    {
        $since = time();

        $container = new Container(['Image' => 'ubuntu:precise', 'Cmd' => ['/bin/true']]);

        $containerManager = $this->getDocker()->getContainerManager();
        $containerManager->create($container);

        $eventManager = $this->getDocker()->getEventManager();
        $events = $eventManager->getEvents($since, time());

        $this->assertNotEmpty($container->getId());
        $this->assertThat($events, new PHPUnit_Framework_Constraint_Callback(function ($events) use($container) {
            foreach ($events as $event) {
                if ($event->getStatus() == 'create' && $event->getId() == $container->getId()) return true;
            }
            return false;
        }));
    }
    
}
