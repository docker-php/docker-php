<?php

namespace Docker\Tests\Manager;

use Docker\Tests\TestCase;

class ImageManagerTest extends TestCase
{
    /**
     * Return a container manager
     *
     * @return \Docker\Manager\ImageManager
     */
    private function getManager()
    {
        return $this->getDocker()->getImageManager();
    }

    public function testFind()
    {
        $manager = $this->getManager();
        $image   = $manager->find('test', 'foo');

        $this->assertEquals('test', $image->getRepository());
        $this->assertEquals('foo', $image->getTag());
        $this->assertNotNull($image->getId());
    }

    public function testFindInexistant()
    {
        $manager = $this->getManager();

        $this->setExpectedException('\\Docker\\Exception\\ImageNotFoundException', 'Image not found');
        $manager->find('test');
    }

    public function testPull()
    {
        $manager = $this->getManager();
        $image = $manager->pull('ubuntu', 'precise');

        $this->assertEquals('ubuntu', $image->getRepository());
        $this->assertEquals('precise', $image->getTag());
        $this->assertNotNull($image->getId());
    }

    public function testFindAll()
    {
        $manager = $this->getManager();

        $this->assertInternalType('array', $manager->findAll());
        $this->assertGreaterThanOrEqual(1, count($manager->findAll()));
    }

    public function testDelete()
    {
        $manager = $this->getManager();

        $image = $manager->find('ubuntu', 'precise');
        $manager->delete($image);

        $this->setExpectedException('\\Docker\\Exception\\ImageNotFoundException', 'Image not found');
        $manager->inspect($image);
    }
}