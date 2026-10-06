Docker PHP
==========

**Docker PHP** (for lack of a better name) is a [Docker](https://www.docker.com/) client written in PHP.
This library aims to reach 100% API support of the Docker Engine.

[![Documentation](https://img.shields.io/badge/docs-Mintlify-blue?style=flat-square)](https://docker-php.mintlify.site/)
[![Latest Version](https://img.shields.io/github/release/docker-php/docker-php.svg?style=flat-square)](https://github.com/docker-php/docker-php/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Total Downloads](https://img.shields.io/packagist/dt/docker-php/docker-php.svg?style=flat-square)](https://packagist.org/packages/docker-php/docker-php)

## Documentation

The [version 3.0 documentation](https://docker-php.mintlify.site/) is available
and is still being updated ahead of the release. Its source lives in
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

We're preparing new v3.x releases, starting with Docker Engine API v1.45. We plan
to add the missing API versions and bring support up to the latest Docker Engine
API. See [Upgrading to 3.0](#upgrading-to-30) below for how to migrate from
existing releases.

Version 3.0 has not been published yet. The installation and migration
instructions below apply once it is available.

## Requirements

- PHP 8.1 or later, with the `mbstring` extension.
- [Composer](https://getcomposer.org/).
- Access to a Docker daemon that accepts API v1.45 requests.

## Installation

```bash
composer require "docker-php/docker-php:^3.0"
```

Composer installs `docker-php/docker-php-api` as a dependency. The 3.0 release
targets Docker API v1.45 and requires the matching generated API package.

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

See the official [Docker Engine API v1.45 reference](https://docs.docker.com/reference/api/engine/version/v1.45/)
for endpoint descriptions, parameters and response schemas matching this API line.

The client package follows semantic versioning. The generated API package uses
`Jane-major.Docker-major.Docker-minor.revision`: `7.1.45.0` means Docker API 1.45,
generated with Jane 7, revision 0.

Keep generated API dependencies within one Docker specification. The planned
3.0 client uses this range:

```json
"docker-php/docker-php-api": ">=7.1.45.0 <7.1.46.0"
```

With the default connection factory, `DOCKER_API_VERSION` overrides the version
in request URLs:

```bash
DOCKER_API_VERSION=1.52 php your-script.php
```

The generated endpoints and models still describe API v1.45. Changing the URL
version does not add newer API fields or endpoints. The client does not
automatically negotiate an API version with the daemon.

## Upgrading to 3.0

### From docker-php 2.x

Version 3.0 requires PHP 8.1 or later, PSR-7 v2 and Jane 7-generated models.
Review endpoint signatures, model types and HTTP client dependencies before
upgrading. Check log and exec consumers against the
[streaming documentation](https://docker-php.mintlify.site/reference/streams).

Change the client requirement to `docker-php/docker-php:^3.0`. Remove an explicit
`docker-php/docker-php-api:4.1.*` requirement, or change it to the API 1.45 range
shown above. Update dependencies, test your application and commit its updated
`composer.lock`.

### From beluga-php

Version 3.0 continues the client maintained under `beluga-php/docker-php`.
The PHP namespaces remain `Docker` and `Docker\API`.

Remove the `beluga-php/docker-php` requirement and any explicit
`beluga-php/docker-php-api` requirement from `composer.json`. Add
`docker-php/docker-php:^3.0`; add the API 1.45 range above if your application
requires the generated API package directly. Resolve the package changes
together, then test the application and commit its updated `composer.lock`.

Install one package family at a time: the Beluga and original packages contain
the same PHP classes.

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
[beluga-php](https://github.com/beluga-php) before the planned return to the
original repositories.

## License

[MIT](LICENSE).
