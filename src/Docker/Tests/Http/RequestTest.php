<?php

namespace Docker\Tests\Http;

use Docker\Http\Request;

use PHPUnit_Framework_TestCase;

class RequestTest extends PHPUnit_Framework_TestCase
{
    public function testBase()
    {
        $request = new Request('GET', '/example');

        $this->assertEquals("GET /example HTTP/1.1\r\nConnection: close\r\n\r\n", (string) $request);
    }

    public function testWithProtocolVersion()
    {
        $request = new Request('GET', '/example');
        $request->setProtocolVersion('1.0');

        $this->assertEquals("GET /example HTTP/1.0\r\n\r\n", (string) $request);
    }

    public function testUriTemplate()
    {
        $request = new Request('GET', ['/foo/{bar}', ['bar' => 'lol']]);

        $this->assertEquals('/foo/lol', $request->getRequestUri());
    }

    public function testHttp11IncludesConnectionClose()
    {
        $request = new Request('GET', '/example');
        $request->setProtocolVersion('1.1');

        $expected = 
            "GET /example HTTP/1.1\r\n".
            "Connection: close\r\n\r\n";

        $this->assertEquals($expected, (string) $request);
    }

    public function testWithContent()
    {
        $request = new Request('GET', '/example', [
            'Content-Type' => 'text/plain',
        ]);
        
        $request->setContent('foobar content');

        $expected = 
            "GET /example HTTP/1.1\r\n".
            "Connection: close\r\n".
            "Content-Length: 14\r\n".
            "Content-Type: text/plain\r\n".
            "\r\n".
            "foobar content";

        $this->assertEquals($expected, (string) $request);
    }
}