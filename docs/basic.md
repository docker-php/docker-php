Once `docker-php` is installed through composer you can start using it:

## Connecting

By default, Docker-PHP does not make any asumption on where your docker daemon is, you will need to specify the entrypoint when creating the client.

By default, Docker-PHP uses the `DOCKER_HOST` environment variable to connect to a running `dockerd`, if not set it will use `unix:///var/run/docker.sock`.
You can, however, connect to an arbitrary server by passing an instance of the transport entrypoint `Docker\Http\Client`:

```php
<?php

$client = new Docker\Http\DockerClient(array(), 'tcp://127.0.0.1');
$docker = new Docker\Docker($client);
```

`DockerClient` is in fact a Guzzle Client with some default options.