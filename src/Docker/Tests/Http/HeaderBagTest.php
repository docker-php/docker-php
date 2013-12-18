<?php

namespace Docker\Tests\Http;

use Docker\Http\HeaderBag;

use PHPUnit_Framework_TestCase;

class HeaderBagTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $bag = new HeaderBag(['Content-Type' => 'text/plain']);

        $this->assertEquals('text/plain', $bag->get('Content-Type'));
    }

    public function testReplace()
    {
        $bag = new HeaderBag(['Content-Type' => 'text/plain']);
        $bag->replace(['Content-Length' => 42]);

        $this->assertEquals(42, $bag->get('Content-Length'));
        $this->assertFalse($bag->has('Content-Type'));
    }

    public function testCount()
    {
        $bag = new HeaderBag(['Content-Type' => 'text/plain']);

        $this->assertEquals(1, count($bag));
    }

    public function testSet()
    {
        $bag = new HeaderBag();
        $bag->set('Content-Type', 'text/plain');

        $this->assertEquals('text/plain', $bag->get('Content-Type'));
    }

    public function testRemove()
    {
        $bag = new HeaderBag(['Content-Type' => 'text/plain']);
        $bag->remove('Content-Type');

        $this->assertFalse($bag->has('Content-Type'));
    }

    public function testFormat()
    {
        $bag = new HeaderBag([
            'Host' => 'example.com',
            'Content-Type' => 'text/plain',
        ]);

        $expected = 
            "Content-Type: text/plain\r\n".
            "Host: example.com";

        $this->assertEquals($expected, $bag->format());
    }
}