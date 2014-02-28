<?php

namespace Docker\Tests\Http;

use Docker\Http\ChunkedResponse;
use Docker\Http\ResponseParser;

class ChunkedResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testRead()
    {
        $rawResponse = <<<EORESPONSE
HTTP/1.1 200 OK
Content-Type: text/plain; charset=utf-8
Connection: close
Transfer-Encoding: chunked

09
aaaaaaaa

08
bbbbbbb

0

EORESPONSE
;

        $stream = fopen('php://memory', 'w+');
        fwrite($stream, $rawResponse);
        rewind($stream);

        $responseParser = new ResponseParser();
        $response = $responseParser->parse($stream, false);

        $this->assertInstanceOf('\Docker\Http\ChunkedResponse', $response);
        $actualRead = "";
        $response->read(function ($content) use(&$actualRead) {
            $actualRead .= $content."test\n";
        });

        $this->assertEquals(<<<EOS
aaaaaaaa
test
bbbbbbb
test

EOS
            , $actualRead);

        $this->assertEquals(<<<EOS
aaaaaaaa
bbbbbbb

EOS
            , $response->getContent());

        $this->assertFalse(is_resource($stream));
    }
}