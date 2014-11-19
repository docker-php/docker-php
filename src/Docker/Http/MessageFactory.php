<?php

namespace Docker\Http;

use Docker\Http\Stream\Filter\OutputEvent;
use GuzzleHttp\Message\MessageFactory as BaseMessageFactory;
use GuzzleHttp\Message\RequestInterface;

class MessageFactory extends BaseMessageFactory
{
    protected function add_callback(RequestInterface $request, callable $callback)
    {
        $request->getEmitter()->on('response.output', function (OutputEvent $event) use ($callback) {
            $callback($event->getContent(), $event->getType());
        });
    }

    protected function add_wait(RequestInterface $request, $wait)
    {
        $request->getConfig()->set('wait', $wait);
    }
}
