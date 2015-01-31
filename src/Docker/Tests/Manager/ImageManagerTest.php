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
        $image = $manager->pull('ubuntu', 'vivid');

        $this->assertEquals('ubuntu', $image->getRepository());
        $this->assertEquals('vivid', $image->getTag());
        $this->assertNotNull($image->getId());
    }

    public function testFindAll()
    {
        $manager = $this->getManager();

        $this->assertInternalType('array', $manager->findAll());
        $this->assertGreaterThanOrEqual(1, count($manager->findAll()));
    }

    public function testRemove()
    {
        $manager = $this->getManager();

        $image = $manager->find('ubuntu', 'vivid');
        $manager->remove($image, true);

        $this->setExpectedException('\\Docker\\Exception\\ImageNotFoundException', 'Image not found');
        $manager->inspect($image);
    }

    public function testSearch()
    {
        $manager = $this->getManager();

        $result = $manager->search('test-image-not-exist');
        $this->assertEmpty($result);

        $this->setExpectedException('\\Docker\\Exception\\APIException', 'Invalid namespace name (a), only [a-z0-9_] are allowed, size between 4 and 30');
        $manager->search('a/test');
    }
}
