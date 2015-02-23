<?php

namespace Docker\Tests;

use Docker\Image;
use PHPUnit_Framework_TestCase;

class ImageTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider provider
     */
    public function testWithRepoAndTags($repo, $tag, $string, $id)
    {
        $image = new Image($repo, $tag);
        $image->setId($id);

        $this->assertEquals($string, $image->__toString());
        // naive getter tests
        $this->assertEquals($repo, $image->getRepository());
        $this->assertEquals($id, $image->getId());
        $this->assertEquals($tag, $image->getTag());

    }

    /**
     * @dataProvider provider
     */
    public function testWithRepoThenTags($repo, $tag, $string, $id)
    {
        $image = new Image($repo);
        $image->setId($id);

        if (empty($repo)) {
            $this->assertEquals($id, $image->__toString());
        } else {
            $this->assertEquals("{$repo}:latest", $image->__toString());
        }

        // naive getter tests
        $this->assertEquals($repo, $image->getRepository());
        $this->assertEquals($id, $image->getId());
        $this->assertEquals('latest', $image->getTag());

        // check is it ok after tag setting
        $image->setTag($tag);
        $this->assertEquals($string, $image->__toString());
    }


    public function provider()
    {
        return [
            [
                'testuser/testimage',
                'sometag',
                'testuser/testimage:sometag',
                'long_image_id_here'
            ],

            [
                'testuser/testimage',
                '', // empty tag
                'testuser/testimage',
                'long_image_id_here'
            ],

            [
                '', // empty repo
                '', // empty tag
                'long_image_id_here',
                'long_image_id_here'
            ]

        ];
    }

}
