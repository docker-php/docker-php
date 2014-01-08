<?php

namespace Docker\Tests\Http;

use Docker\Http\ResponseParser;

use PHPUnit_Framework_TestCase;

class ResponseParserTest extends PHPUnit_Framework_TestCase
{
    public function testParse()
    {
        $raw = <<<RAW
HTTP/1.0 200 OK
Content-Type: application/json
Date: Wed, 18 Dec 2013 03:43:33 GMT

foobar content
RAW;

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $raw);
        rewind($stream);

        $parser = new ResponseParser();
        $response = $parser->parse($stream);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
        $this->assertEquals('foobar content', $response->getContent());
    }

    public function testRead()
    {
        $raw = <<<RAW
HTTP/1.0 200 OK
Content-Type: application/json
Date: Wed, 18 Dec 2013 03:43:33 GMT

foobar content
RAW;

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $raw);
        rewind($stream);

        $parser     = new ResponseParser();
        $response   = $parser->parse($stream);
        $actualRead = "";

        $response->read(function ($content) use(&$actualRead) {
            $actualRead .= $content;
        });

        $this->assertEquals("foobar content", $actualRead);
    }
}