Once `docker-php` is installed through composer you can start using it:

## Connecting

By default, Docker-PHP does not make any assumption on where your docker daemon is, you will need to specify the entrypoint when creating the client.

By default, Docker-PHP uses the `DOCKER_HOST` environment variable to connect to a running `dockerd`, if not set it will use `unix:///var/run/docker.sock`.
You can, however, connect to an arbitrary server by passing an instance of the transport entrypoint `Docker\Http\Client`:

```php
<?php

$client = new Docker\Http\DockerClient(array(), 'tcp://127.0.0.1');
$docker = new Docker\Docker($client);
```

`DockerClient` is in fact a Guzzle Client with some default options.


## Integration with Symfony

To integrate Docker-PHP into your Symfony Framework project we recommend creating a service in the service (dependency injection) container:

**Example configuration:** connecting to your local boot2docker machine with TLS disabled

```yml
parameters:
    docker_entrypoint: 'tcp://192.168.59.103:2375'

services:
    docker.client:
        class: Docker\Http\DockerClient
        arguments:
            - []
            - %docker_entrypoint%
            - ~
            - false
    docker:
        class: Docker\Docker
        arguments:
            - @docker.client

```

**Example configuration:** connecting to your local boot2docker machine with TLS enabled (default)

Create a combined key file first:

```sh
cd ~/.boot2docker/certs/boot2docker-vm/
cat key.pem > comb.pem
cat cert.pem >> comb.pem
cat ca.pem >> comb.pem
```


```yml
parameters:
    docker_entrypoint: 'tcp://192.168.59.103:2376'
    docker_cert_path: '/Users/username/.boot2docker/certs/boot2docker-vm'

services:
    docker.client:
        class: Docker\Http\DockerClient
        arguments:
            - []
            - %docker_entrypoint%
            -
                ssl:
                    local_cert: '%docker_cert_path%/comb.pem'
            - true
    docker:
        class: Docker\Docker
        arguments:
            - @docker.client

```


Docker-PHP is now available as `docker`. If you extend the default Controller from the Symfony FrameworkBundle you can write:

```php
use Docker\Docker;

class ...
{
    public function indexAction()
    {
        /** @var Docker $docker */
        $docker = $this->get('docker');
        
        $imageManager = $docker->getImageManager();
        $containerManager = $docker->getContainerManager();
    }
}

```