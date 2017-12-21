<?php

declare(strict_types=1);

namespace Docker\Amp;

use Amp\CancellationToken;
use Amp\Promise;
use Amp\Socket\BasicSocketPool;
use Amp\Socket\ClientSocket;
use Amp\Socket\SocketPool;

class FixedSocketPool implements SocketPool
{
    private $uri;

    private $socketPool;

    public function __construct(string $uri, SocketPool $socketPool = null)
    {
        if (null === $socketPool) {
            $socketPool = new BasicSocketPool();
        }

        $this->uri = $uri;
        $this->socketPool = $socketPool;
    }

    public function checkout(string $uri, CancellationToken $token = null): Promise
    {
        return $this->socketPool->checkout($this->uri, $token);
    }

    public function checkin(ClientSocket $socket): void
    {
        $this->socketPool->checkin($socket);
    }

    public function clear(ClientSocket $socket): void
    {
        $this->socketPool->clear($socket);
    }
}
