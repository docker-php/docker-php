# Asynchronous Client

Starting from 2.0, Docker-PHP proposes an Asynchronous PHP Client using [Amp](https://amphp.org/) and 
[Artax](https://amphp.org/artax/).

## Installation

Since it's optional you will have to require artax with composer to use it:

```
composer require amphp/artax:^3.0
```

## Usage

Then you can use the `DockerAsync` API Client:

```php
<?php

use Amp\Loop;
use Docker\API\Model\ContainersCreatePostBody;
use Docker\DockerAsync;

Loop::run(function () {
    $docker = DockerAsync::create();

    $containerConfig = new ContainersCreatePostBody();
    $containerConfig->setImage('busybox:latest');
    $containerConfig->setCmd(['echo', '-n', 'output']);
    $containerConfig->setAttachStdout(true);
    $containerConfig->setLabels(new \ArrayObject(['docker-php-test' => 'true']));

    $response = yield $docker->imageCreate(null, [
        'fromImage' => 'busybox:latest',
    ], DockerAsync::FETCH_RESPONSE);

    yield $response->getBody();

    $containerCreate = yield $docker->containerCreate($containerConfig);
    $containerStart = yield $docker->containerStart($containerCreate->getId());
    /** @var \Docker\API\Model\ContainersIdJsonGetResponse200 $containerInfo */
    $containerInfo = yield $docker->containerInspect($containerCreate->getId());

    var_dump($containerInfo->getName());
});
```

API of `DockerAsync` is exactly the same as `Docker`, at the exception that each endpoint will always return an `Amp\Promise`
object, which allows to you to do parallelism and await them with the `yield` keyword.

If you are not familiar with this kind of API, please look at the [Amp documentation on that subject](https://amphp.org/getting-started/)
