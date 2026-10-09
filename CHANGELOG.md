# Changelog

## 4.0.1

### Fixed

- Stop lowest-dependency installs from pulling in the abandoned
  `php-http/message-factory` package. The client now conflicts with Jane
  runtimes before 7.14.4, the version that generates the API packages, and with
  `php-http/message` before 1.16.

## 4.0.0

### Breaking changes

- Allow every Docker Engine API line from v1.45 to v1.56
  (`docker-php/docker-php-api` `>=7.1.45.0 <7.1.57.0`). Composer installs the
  newest line unless you require one; generated model names and fields differ
  between lines. Require `>=7.1.45.0 <7.1.46.0` to keep the 3.x models.
- Negotiate the request API version with the daemon when neither `api_version`
  nor `DOCKER_API_VERSION` is set: the bundled factory sends an unversioned
  `/_ping` first and uses the lower of the daemon's and the installed line's
  versions.
- Throw `UnexpectedClientErrorException` or `UnexpectedServerErrorException` for
  error statuses the generated endpoints do not handle. Call
  `throwOnUnexpectedStatus(false)` to return `null` instead.
- Apply a context's `.dockerignore` by default. Call `applyDockerignore(false)`
  to archive the whole directory.
- Add an optional `$accept` parameter to `Docker::systemEvents()`, matching API
  1.52 and later. Subclasses that override it need the same parameter.

### Added

- `DockerClientFactory::defaultApiVersion()` returns the installed API line's
  version.
- `FrameError::message()` reads the error from build, pull and push progress
  frames on every API line.
- Stats samples decode into `ContainerStatsResponse` models from API 1.48.

### Fixed

- Decode integers above `PHP_INT_MAX`, such as the `pids_stats.limit` Docker
  reports for a container without a PID limit, as `PHP_INT_MAX`. The generated
  integer setters rejected them as floats.

### Removed

- The 3.3 deprecation notices, and the direct dependency on
  `symfony/deprecation-contracts`.

## 3.3.0

- Add `StatsStream`: `containerStats()` streams samples to `onFrame()` by
  default instead of blocking on the endless response.
- Add `stop()` to callback streams, so build, pull, push, event and stats
  readers can finish from a callback.
- Add `close()` to `AttachWebsocketStream`.
- Accept a list of tags for `imageBuild()`'s `t` option.
- Add opt-in exceptions for error statuses the generated endpoints do not
  handle (`Docker::throwOnUnexpectedStatus()`). Without it, such calls still
  return `null` and trigger a deprecation; throwing becomes the default in 4.0.
- Add opt-in `.dockerignore` support for build contexts
  (`Context::applyDockerignore()`). Without it, a context with a
  `.dockerignore` triggers a deprecation; applying it becomes the default
  in 4.0.
- Fix `containerAttachWebsocket()`, which returned `null` for every real
  WebSocket upgrade.
- Fix `containerAttach()` losing TTY output, and handle upgraded attach
  responses.
- Fix WebSocket close, ping and empty frames, stalled or truncated frames,
  65,536-byte payload lengths, mask key randomness and partial writes.
- Declare the stream return types of `Docker` methods for static analysis.
- Allow Guzzle 8 and PHPStan 2 for development.

## 3.2.0

- Add opt-in interactive exec sessions with nonblocking stdin writes, bounded
  output polling, non-TTY stdin EOF and streaming deadlines.
- Keep existing blocking exec APIs unchanged. Interactive sessions require
  the bundled socket transport; TTY stdin half-close is rejected.

## 3.1.0

- Add an explicit `api_version` factory option. It overrides `DOCKER_API_VERSION`
  without changing the process environment or generated models.
- Preserve the existing environment fallback and default API version 1.45.

## 2.0

 - [BC Break] All endpoints have new names and potentially new parameters
 - Add async (with amp artax) support
 - It now uses the official swagger specification of docker
 - Allow to use from 1.25 to 1.36 api version of Docker
 - Add support for more keywords in ContextBuilder
 - Lot of bug fixes

## 1.24.0 - 08/08/2014

 - [BC Break] Listing containers now return `ContainerInfo` object (instead of `ContainerConfig`)
