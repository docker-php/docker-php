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

    public function testFindAllAll()
    {
        $manager = $this->getManager();

        $images1 = $manager->findAll();
        $images2 = $manager->findAll(false, true);

        $this->assertInternalType('array', $images2);
        $this->assertGreaterThan(count($images1), count($images2));
    }

    public function testFindAllDangling()
    {
        $manager = $this->getManager();

        $images = $manager->findAll(true);

        $this->assertInternalType('array', $images);
        $this->assertGreaterThanOrEqual(1, count($images));
    }

    public function testRemove()
    {
        $manager = $this->getManager();

        $image = $manager->find('ubuntu', 'vivid');
        $manager->remove($image, true);

        $this->setExpectedException('\\Docker\\Exception\\ImageNotFoundException', 'Image not found');
        $manager->inspect($image);
    }

    public function testRemoveImages()
    {
        $containers = ['ubuntu:precise', '69c02692b0c1'];
        $manager = $this
            ->getMockBuilder('\Docker\Manager\ImageManager')
            ->setMethods(['remove'])
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects($this->exactly(2))
            ->method('remove')
            ->with($this->isInstanceOf('\Docker\Image'), false, false)
            ->will($this->returnSelf());

        $manager->removeImages($containers);
    }

    public function testSearch()
    {
        $manager = $this->getManager();

        $result = $manager->search('test-image-not-exist');
        $this->assertEmpty($result);

        $this->setExpectedException('\\Docker\\Exception\\APIException', 'Invalid namespace name');
        $manager->search('a/test');
    }

    public function testTag()
    {
        $image = $this->getManager()->find('test', 'foo');

        $this->getManager()->tag($image, 'docker-php/unit-test', 'latest');

        $this->assertEquals('docker-php/unit-test', $image->getRepository());
        $this->assertEquals('latest', $image->getTag());

        $newImage = $this->getManager()->find('docker-php/unit-test', 'latest');
        $this->assertEquals($image->getId(), $newImage->getId());

        $this->getManager()->removeImages(array($newImage));
    }

    public function testHistory()
    {
        $image = $this->getManager()->find('test', 'foo');
        $history = $this->getManager()->history($image);

        $this->assertGreaterThan(1, count($history));
        $this->assertEquals('/bin/true', $history[0]['CreatedBy']);
    }
}
