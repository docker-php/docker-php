<?php

namespace Docker\Http;

use GuzzleHttp\Message\MessageFactory as BaseMessageFactory;
use GuzzleHttp\Message\RequestInterface;

class MessageFactory extends BaseMessageFactory
{
    /**
     * @param array $customOptions Associative array of custom request option
     *                             names mapping to functions used to apply
     *                             the option. The function accepts the request
     *                             and the option value to apply.
     */
    public function __construct(array $customOptions = [])
    {
        $customOptions['callback'] = [$this, 'addCallback'];
        $customOptions['wait'] = [$this, 'addWait'];

        parent::__construct($customOptions);
    }

    protected function addCallback(RequestInterface $request, callable $callback)
    {
        $request->getConfig()->set('callback', $callback);
    }

    protected function addWait(RequestInterface $request, $wait)
    {
        $request->getConfig()->set('wait', $wait);
    }
}
