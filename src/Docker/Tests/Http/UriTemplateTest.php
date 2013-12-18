<?php

namespace Docker\Tests\Http;

use Docker\Http\UriTemplate;

use PHPUnit_Framework_TestCase;

class UriTemplateTest extends PHPUnit_Framework_TestCase
{
    public function testExpandSimple()
    {
        $compiler = new UriTemplate();

        $this->assertEquals('/foo/quux', $compiler->compile('/foo/{bar}', ['bar' => 'quux']));
    }

    public function testExpandQueryString()
    {
        $compiler = new UriTemplate();

        $result = $compiler->compile('/foo{?bar*}', ['bar' => ['foo' => 'quux', 'bar' => 'lol']]);

        $this->assertEquals('/foo?foo=quux&bar=lol', $result);
    }
}