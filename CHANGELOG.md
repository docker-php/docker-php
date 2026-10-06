# Changelog

## Unreleased

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
