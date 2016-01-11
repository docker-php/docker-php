Docker PHP
==========

**Docker PHP** (for lack of a better name) is a [Docker](http://docker.com/) client written in PHP.
This library aim to reach 100% API support of the Docker Engine.

The test suite currently passes against the [Docker Remote API v1.21](http://docs.docker.com/reference/api/docker_remote_api_v1.21/).

[![Documentation Status](https://readthedocs.org/projects/docker-php/badge/?version=latest)](http://docker-php.readthedocs.org/en/latest/)
[![Latest Version](https://img.shields.io/github/release/stage1/docker-php.svg?style=flat-square)](https://github.com/stage1/docker-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/stage1/docker-php.svg?branch=master&style=flat-square)](https://travis-ci.org/stage1/docker-php)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/stage1/docker-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stage1/docker-php)
[![Quality Score](https://img.shields.io/scrutinizer/g/stage1/docker-php.svg?style=flat-square)](https://scrutinizer-ci.com/g/stage1/docker-php)
[![Total Downloads](https://img.shields.io/packagist/dt/stage1/docker-php.svg?style=flat-square)](https://packagist.org/packages/stage1/docker-php)



Installation
------------

The recommended way to install Docker PHP is of course to use [Composer](http://getcomposer.org/):

```bash
composer require stage1/docker-php
```

Usage
-----

See [the documentation](http://docker-php.readthedocs.org/en/latest/).

Unit Tests
----------

Setup the test suite using [Composer](http://getcomposer.org/) if not already done:

```
$ composer install --dev
```

Run it using [PHPUnit](http://phpunit.de/):

```
$ composer test
```


Contributing
------------

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.


Credits
-------

This README heavily inspired by [willdurand/Negotiation](https://github.com/willdurand/Negotiation) by @willdurand. This guy is pretty awesome.


License
-------

The MIT License (MIT). Please see [License File](LICENSE) for more information.
