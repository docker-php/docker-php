Docker PHP
==========

**Docker PHP** (for lack of a better name) is a [Docker](http://docker.io/) client written in PHP. This library is still a work in progress. Not much is supported yet, but the goal is to reach 100% API support.

The test suite currently pass against the [Docker Remote API v1.8](http://docs.docker.io/en/latest/api/docker_remote_api_v1.8/).

See https://gist.github.com/ubermuda/7876615 for an overview of how it works. Real documentation will follow soon.

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

In a nutshell, this will connect to Docker at `http://127.0.0.1:4243`:

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

You could also pass in your own instance of `Guzzle\Http\Client` to `Docker`'s constructor. See [Guzzle's documentation](http://docs.guzzlephp.org/en/latest/docs.html) for more information.

**Note**: due to PHP's HTTP libraries limitations, connecting to a Docker **socket** is not supported. You have to use **tcp**. If anyone knows of an HTTP library that supports socket connections, please open a ticket, I'll be more than happy to add support for it.

Unit Tests
----------

Setup the test suite using [Composer](http://getcomposer.org/):

```
$ composer install --dev
```

Run it using [PHPUnit](http://phpunit.de/):

```
$ bin/phpunit
```

Contributing
------------

Here are a few rules to follow in order to ease code reviews, and discussions before maintainers accept and merge your work.

* You **MUST** follow the [PSR-1](http://www.php-fig.org/psr/1/) and [PSR-2](http://www.php-fig.org/psr/2/).
* You **MUST** run the test suite.
* You **MUST** write (or update) unit tests.
* You **SHOULD** write documentation.

Please, write [commit messages that make sense](http://tbaggery.com/2008/04/19/a-note-about-git-commit-messages.html), and [rebase your branch](http://git-scm.com/book/en/Git-Branching-Rebasing) before submitting your Pull Request.

One may ask you to [squash your commits](http://gitready.com/advanced/2009/02/10/squashing-commits-with-rebase.html) too. This is used to "clean" your Pull Request before merging it (we don't want commits such as `fix tests`, `fix 2`, `fix 3`, etc.).

Also, when creating your Pull Request on GitHub, you **MUST** write a description which gives the context and/or explains why you are creating it.

Thank you!

Credits
-------

This README heavily inspired by [willdurand/Negotiation](https://github.com/willdurand/Negotiation) by @willdurand. This guy is pretty awesome.


License
-------

The MIT License (MIT)

Copyright (c) 2013 Geoffrey Bachelet <geoffrey@stage1.io>

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
