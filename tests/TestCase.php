<?php

namespace Docker\Tests;

use Docker\Docker;

class TestCase extends \PHPUnit\Framework\TestCase
{
    private static $docker;

    public static function getDocker(): Docker
    {
        if (null === self::$docker) {
            self::$docker = Docker::create(self::getVersion());
        }

        return self::$docker;
    }

    public static function getVersion()
    {
        $version = getenv('DOCKER_API_VERSION');

        if ($version) {
            return $version;
        }

        return Docker::VERSION_LAST_STABLE;
    }

    public static function createModel($class)
    {
        $class = '\\Docker\\API\\' . self::getVersion() . '\\Model\\' . $class;

        return new $class;
    }
}
