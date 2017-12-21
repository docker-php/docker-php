<?php

declare(strict_types=1);

namespace Docker\Tests;

use Docker\Docker;

class TestCase extends \PHPUnit\Framework\TestCase
{
    private static $docker;

    public static function getDocker(): Docker
    {
        if (null === self::$docker) {
            self::$docker = Docker::create();
        }

        return self::$docker;
    }
}
