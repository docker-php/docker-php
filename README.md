# Docker PHP

**Docker PHP** (for lack of a better name) is a [Docker](http://docker.com/) client written in PHP.
This library aim to reach 100% API support of the Docker Engine.

The test suite currently passes against Docker Remote API v1.25 to v1.36.

[![Documentation Status](https://readthedocs.org/projects/docker-php/badge/?version=latest)](http://docker-php.readthedocs.org/en/latest/)
[![Latest Version](https://img.shields.io/github/release/beluga-php/docker-php.svg?style=flat-square)](https://github.com/beluga-php/docker-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/beluga-php/docker-php.svg?style=flat-square)](https://packagist.org/packages/beluga-php/docker-php)

## Installation

The recommended way to install Docker PHP is of course to use [Composer](http://getcomposer.org/):

```bash
composer require beluga-php/docker-php
```

## Docker API Version

Each release line targets a Docker API version. The 1.45 client uses API v1.45 by default.

You can override the version used in request URLs when connecting to a Docker daemon that no longer accepts v1.45:

```bash
DOCKER_API_VERSION=1.52 php your-script.php
```

This only changes the API version in request URLs. The generated models and endpoints still use the v1.45 specification.

To use an older API specification, install the matching API package:

```bash
composer require "beluga-php/docker-php-api:6.1.41.*"
```

## Usage

See [the documentation](http://docker-php.readthedocs.org/en/latest/).

### Container logs

`containerLogs()` returns a stream. Register callbacks and call `wait()` to read it:

```php
$logs = $docker->containerLogs('test', ['stdout' => true, 'stderr' => true]);
$logs->onStdout(function (string $output): void {
    echo $output;
});
$logs->onStderr(function (string $output): void {
    fwrite(STDERR, $output);
});
$logs->wait();
```

TTY containers combine stdout and stderr; their output goes to `onStdout`.
For responses with the `application/vnd.docker.raw-stream` content type, the
client inspects the container's TTY setting to distinguish terminal output from
the framed logs returned by older Docker APIs. Responses marked
`application/vnd.docker.multiplexed-stream` do not need this extra request.

### Command output

With `Detach` set to `false`, `execStart($execId, $execStartConfig)` returns the
same callback stream. Register `onStdout` / `onStderr` callbacks and call `wait()`.
Use the same `Tty` setting when creating and starting the exec command; TTY output
is combined on `onStdout`.

`executeRawEndpoint()` and the deprecated `FETCH_RESPONSE` mode bypass decoding.
Their response bodies include Docker's binary frame headers for non-TTY output.

## Unit Tests

Setup the test suite using [Composer](http://getcomposer.org/) if not already done:

```
$ composer install --dev
```

Run it using [PHPUnit](http://phpunit.de/):

```
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

This README heavily inspired by [willdurand/Negotiation](https://github.com/willdurand/Negotiation) by @willdurand. This guy is pretty awesome.

This library is a fork of the original [docker-php/docker-php](https://github.com/docker-php/docker-php), created by [Geoffrey Bachelet](https://github.com/ubermuda) and [Joel Wurtz](https://github.com/joelwurtz).

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
