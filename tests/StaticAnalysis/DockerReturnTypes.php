<?php

declare(strict_types=1);

namespace Docker\Tests\StaticAnalysis;

use Docker\Docker;

use function PHPStan\Testing\assertType;

// Analysed by PHPStan, not run by PHPUnit.
function dockerReturnTypes(Docker $docker): void
{
    assertType('Docker\Stream\DockerRawStream|null', $docker->containerAttach('id'));
    assertType('Docker\Stream\AttachWebsocketStream|null', $docker->containerAttachWebsocket('id'));
    assertType('Docker\Stream\DockerRawStream|null', $docker->containerLogs('id'));
    assertType('Docker\Stream\DockerRawStream|null', $docker->execStart('id'));
    assertType('Docker\Stream\BuildStream|null', $docker->imageBuild());
    assertType('Docker\Stream\BuildStream|null', $docker->imageBuild(null, ['t' => ['app:latest', 'app:1.0']]));
    assertType('Docker\Stream\CreateImageStream|null', $docker->imageCreate());
    assertType('Docker\Stream\PushStream|null', $docker->imagePush('name'));
    assertType('Docker\Stream\EventStream|null', $docker->systemEvents());
    assertType('Psr\Http\Message\ResponseInterface', $docker->containerLogs('id', [], Docker::FETCH_RESPONSE));
}
