<?php

declare(strict_types=1);

namespace Docker;

use Docker\API\ClientAsync;

/**
 * Docker\Docker.
 */
class DockerAsync extends ClientAsync
{
    public static function create($httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = DockerAsyncClient::createFromEnv();
        }

        return parent::create($httpClient);
    }
}
