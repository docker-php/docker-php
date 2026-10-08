Docker PHP
==========

**Docker PHP** (for lack of a better name) is a [Docker](https://www.docker.com/) client written in PHP.
This library aims to reach 100% API support of the Docker Engine.

[![Documentation](https://img.shields.io/badge/docs-Mintlify-blue?style=flat-square)](https://docker-php.mintlify.site/)
[![Latest Version](https://img.shields.io/github/release/docker-php/docker-php.svg?style=flat-square)](https://github.com/docker-php/docker-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/docker-php/docker-php.svg?style=flat-square)](https://packagist.org/packages/docker-php/docker-php)

## Documentation

The [version 4.x documentation](https://docker-php.mintlify.site/) is available.
Its source lives in
[docs/](docs/); see [DOCUMENTATION.md](DOCUMENTATION.md) to preview and edit it
locally. The [legacy documentation](https://docker-php.readthedocs.io/en/latest/)
remains available for earlier releases.

## New maintainers

After this repository was archived in 2019, the code was forked in
[beluga-php/docker-php](https://github.com/beluga-php/docker-php), where maintenance
and improvements continued.

We later contacted [joelwurtz](https://github.com/joelwurtz) and agreed to take over
maintenance of the original `docker-php/docker-php` repository to give you a
better upgrade path. Development will continue here, and we will archive
`beluga-php/docker-php` once the migration is complete.

Version 4.0 works with Docker Engine API v1.45 to v1.56. See
[Upgrading to 4.0](#upgrading-to-40) below for how to migrate from existing
releases.

## Requirements

- PHP 8.1 or later, with the `mbstring` extension.
- [Composer](https://getcomposer.org/).
- Access to a Docker daemon with API v1.45 or later (Docker Engine 26.0 or later).

## Installation

```bash
composer require "docker-php/docker-php:^4.0"
```

Composer also installs `docker-php/docker-php-api`, choosing the newest API line
unless you require one. See [Docker API versions](#docker-api-versions).

## Usage

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Docker\Docker;

$docker = Docker::create();

foreach ($docker->containerList(['all' => true]) as $container) {
    printf("%s\t%s\n", $container->getId(), $container->getImage());
}
```

Endpoint methods return generated models where the API defines a response body.
Their PHPDoc lists accepted parameters, return types and API exceptions.

`Docker::create()` connects through `unix:///var/run/docker.sock` by default.
Environment variables are optional. See [connection settings](https://docker-php.mintlify.site/connection)
and the [Guzzle examples](https://docker-php.mintlify.site/guides/guzzle) for
custom sockets, remote daemons and TLS configuration.

For streaming output, see [container logs](https://docker-php.mintlify.site/guides/logs),
[command output](https://docker-php.mintlify.site/guides/exec) and the
[streaming reference](https://docker-php.mintlify.site/reference/streams).
These guides cover callback streams, TTY output and raw response framing.

## Docker API versions

Each Docker Engine API version has its own `docker-php/docker-php-api` line with
generated endpoints and models. The line uses the four-part version
`Jane-major.Docker-major.Docker-minor.revision`: `7.1.56.0` means Docker API 1.56,
generated with Jane 7, revision 0. To use a specific version, require its line:

```bash
composer require "docker-php/docker-php:^4.0" "docker-php/docker-php-api:>=7.1.51.0 <7.1.52.0"
```

Pin a line if your code names generated models or calls methods that differ
between versions. See the official
[Docker Engine API reference](https://docs.docker.com/reference/api/engine/) for
each version's endpoints, parameters and response schemas.

The bundled connection factory negotiates the version in request URLs: it uses
the lower of the installed line and the daemon's maximum API version, as the
Docker CLI does. `DOCKER_API_VERSION` or the factory's `api_version` option fix
the version instead. Changing the URL version does not change the generated
models. See [API versions](https://docker-php.mintlify.site/api-versions).

## Upgrading to 4.0

Most applications come from the original docker-php 2.x client or from
`beluga-php/docker-php` 1.45.x. Follow the section for your current package,
then check the [3.x changes](#from-docker-php-3x), because 4.0 also changes
defaults that 2.x and Beluga applications rely on.

### From docker-php 2.x

Version 4.0 requires PHP 8.1 or later, PSR-7 v2 and Jane 7-generated models.
Review endpoint signatures, model types and HTTP client dependencies before
upgrading. Check log and exec consumers against the
[streaming documentation](https://docker-php.mintlify.site/reference/streams).

Change the client requirement to `docker-php/docker-php:^4.0`. Remove an explicit
`docker-php/docker-php-api:4.1.*` requirement, or change it to one API line as
shown above. Update dependencies, test your application and commit its updated
`composer.lock`.

### From beluga-php

Version 4.0 continues the client maintained under `beluga-php/docker-php`.
The PHP namespaces remain `Docker` and `Docker\API`.

Remove the `beluga-php/docker-php` requirement and any explicit
`beluga-php/docker-php-api` requirement from `composer.json`. Add
`docker-php/docker-php:^4.0`; to keep the Beluga v1.45 models, also require
`docker-php/docker-php-api:>=7.1.45.0 <7.1.46.0`. Resolve the package changes
together, then test the application and commit its updated `composer.lock`.

Install one package family at a time: the Beluga and original packages contain
the same PHP classes.

### From docker-php 3.x

- Composer now installs the newest API line unless you require one. Require
  `docker-php/docker-php-api:>=7.1.45.0 <7.1.46.0` to keep the 3.x models.
- Requests negotiate the API version with the daemon unless you set one.
- Error statuses the generated endpoints do not handle throw an
  `UnexpectedStatusCodeException`. Call `throwOnUnexpectedStatus(false)` to
  return `null` instead.
- `Context` applies `.dockerignore`. Call `applyDockerignore(false)` to archive
  the whole directory.

Applications that cannot upgrade yet can stay on `^3.3`. See the
[upgrade guide](https://docker-php.mintlify.site/migration#from-3-x) for model
changes between API versions.

## Development

Install dependencies and pull the image used by the integration tests:

```bash
composer install
docker pull busybox:latest
```

Run the test suite and static analysis:

```bash
composer run-script test-ci
composer run-script phpstan
```

The integration tests create Docker resources on the configured daemon. Use a
development daemon when running them. See [CONTRIBUTING.md](CONTRIBUTING.md) for
contribution instructions.

## Credits

Docker PHP was created by [Geoffrey Bachelet](https://github.com/ubermuda) and
[Joel Wurtz](https://github.com/joelwurtz). Development continued under
[beluga-php](https://github.com/beluga-php) before the return to the
original repositories.

## License

[MIT](LICENSE).
