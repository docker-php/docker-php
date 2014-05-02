Docker PHP
==========

**Docker PHP** (for lack of a better name) is a [Docker](http://docker.io/) client written in PHP. This library is still a work in progress. Not much is supported yet, but the goal is to reach 100% API support.

The test suite currently passes against the [Docker Remote API v1.9](http://docs.docker.io/en/latest/api/docker_remote_api_v1.9/).

Versionning
-----------

There is no *stable* version yet and the API is rapidly evolving, but we still try to semantically version the library according to [semver](http://semver.org/), but shifted a little bit:

* **MAJOR** version number stays to 0 until API freeze
* **MINOR** version number is incremented when a backward incompatible change is made
* **PATCH** version number is incremented when a new feature is added

So basically, if you want the `0.5` version set, use a version constraint of `~0.5` and you should be fine.

We are **NOT** documenting upgrade procedures until we reach a stable API, please read the code and PRs to keep up with what's going on. You can also ask us for help, we're nice people!

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

See [the documentation](https://github.com/stage1/docker-php/blob/master/doc/usage.md).

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

Projects
--------

Projects known to be using docker-php:

* [JoliCi](https://github.com/jolicode/JoliCi), Run your tests on different and isolated stacks

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
