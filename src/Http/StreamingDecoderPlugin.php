<?php

declare(strict_types=1);

namespace Docker\Http;

use Docker\Stream\DechunkStream;
use Docker\Stream\SocketReadStream;
use Http\Client\Common\Plugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Socket\Stream as SocketStream;
use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final class StreamingDecoderPlugin implements Plugin
{
    private DecoderPlugin $decoder;

    public function __construct(array $config = [])
    {
        $this->decoder = new DecoderPlugin($config);
    }

    public function handleRequest(RequestInterface $request, callable $next, callable $first): Promise
    {
        return $this->decoder->handleRequest($request, static function (RequestInterface $request) use ($next): Promise {
            return $next($request)->then(static function (ResponseInterface $response): ResponseInterface {
                if ($response->getBody() instanceof SocketStream) {
                    $response = $response->withBody(new SocketReadStream($response->getBody()));
                }
                if ('chunked' === strtolower(trim($response->getHeaderLine('Transfer-Encoding')))) {
                    return $response->withBody(new DechunkStream($response->getBody()))->withoutHeader('Transfer-Encoding');
                }

                return $response;
            });
        }, $first);
    }
}
