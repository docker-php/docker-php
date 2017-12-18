<?php

namespace Docker;

use Docker\API\ClientAsync;

/**
 * Docker\Docker
 */
class DockerAsync extends ClientAsync
{
    public const FETCH_STREAM = 'stream';

    public static function create($httpClient = null)
    {
        if ($httpClient === null) {
            $httpClient = DockerAsyncClient::createFromEnv();
        }

        return parent::create($httpClient);
    }
}
