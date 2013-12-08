Docker PHP
==========

**Docker PHP** (for lack of a better name) is a [Docker](http://docker.io/) client written in PHP. This library is still a work in progress. Not much is supported yet, but the goal is to reach 100% API support.

Installation
------------

The recommended way to install Docker PHP is of course to use [Composer](http://getcomposer.org/):

```json
{
    "require": {
        "stage1/docker-php": "@dev"
    }
}
```

**Note**: there is no stable version of Docker PHP yet.

Usage
-----

In a nutshell:

```php
<?php

use Docker\Docker;
use Docker\Container;

$docker = new Docker();

$container = new Container([
    'Image' => 'ubuntu:precise',
    'Cmd' => ['/bin/true']
]);

$docker->getContainerManager()->run($container);

```

**Note**: due to PHP's HTTP libraries limitations, connecting to a Docker **socket** is not supported. You have to use **tcp**. If anyone knows of an HTTP library that supports socket connections, please open a ticket, I'll be more than happy to add support for it.