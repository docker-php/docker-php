<?php

declare(strict_types=1);

namespace Docker;

use Docker\API\Client;
use Docker\Resource\ContainerResourceTrait;
use Docker\Resource\ExecResourceTrait;
use Docker\Resource\ImageResourceTrait;
use Docker\Resource\SystemResourceTrait;

/**
 * Docker\Docker.
 */
class Docker extends Client
{
    public const FETCH_STREAM = 'stream';

    use ContainerResourceTrait;
    use ImageResourceTrait;
    use ExecResourceTrait;
    use SystemResourceTrait;

    public static function create($httpClient = null)
    {
        if (null === $httpClient) {
            $httpClient = DockerClientFactory::createFromEnv();
        }

        return parent::create($httpClient);
    }
}
