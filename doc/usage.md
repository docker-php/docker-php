# Docker-PHP

* [Connecting to Docker](#connecting-to-docker)
* [Running a container](#running-a-container)
 * [Streaming a running container's output](#streaming-a-running-containers-output)
 * [Running a container as a daemon](#running-a-container-as-a-daemon)
* [Creating, starting, attaching and waiting for containers](#creating-starting-attaching-and-waiting-for-containers)
* [Mapping a container's ports](#mapping-a-containers-ports)
* [Finding and inspecting Containers](#finding-and-inspecting-containers)
* [Stopping and removing containers](#stopping-and-removing-containers)
* [The Docker\Container class](#the-dockercontainer-class)
 * [Configuring exposed ports](#configuring-exposed-ports)

## Connecting to Docker

By default, Docker-PHP tries to connect to a running `dockerd` instance at `127.0.0.1:43`. You can, however, connect to an arbitrary server by passing an instance of the `Docker\Http\Client`:

```php
<?php

$client = new Docker\Http\Client('unix:///var/run/docker.sock');
$docker = new Docker\Docker($client);
```

## Running a container

Use `Docker\Manager\ContainerManager#run()` to run a container.

```php
<?php

$container = new Container(['Image' => 'ubuntu:precise']);
$docker->run($container);
```

The `run()` method provides a few options to handle the workflow of your container.

### Streaming a running container's output

You can automatically attach a created container and read its output by using a callback.
The callback function receives two arguments: the type of stream, and a piece of content. The type can be either `0`, `1` or `2`, denoting respectively `stdin`, `stdout` and `stderr` (as per [Docker's Attach multiplexing protocol](http://docs.docker.io/en/latest/reference/api/docker_remote_api_v1.9/#attach-to-a-container)).

```php
<?php

$manager = $docker->getContainerManager();
$manager->run($container, function($type, $chunk) {
    fputs($type === 1 ? STDOUT : STDERR, $chunk);
});
```

### Running a container as a daemon

You can run a container as a daemon (effectively making the `run()` method non-blocking) by passing `true` as its fourth argument.

```php

<?php

$docker->run($container, $printCallback, true);
```


## Creating, starting, attaching and waiting for containers

The `run()` command is actually a composite of `create()`, `start()`, `attach` and `wait`, just like the `docker run` CLI command that you might be used to. You can use these methods to gain more fine-grained control over your containers' workflow.

```php
<?php

$container = new Container(['Image' => 'ubuntu:precise']);

$manager = $docker->getContainerManager();
$manager->create($container);

printf('Created container with Id "%s"', $container->getId());

$manager->attach($container)->readAttach(function($type, $chunk) {
    print($chunk);
});

$manager->start($container);
$manager->wait($container);
```

The `attach()` method can also retrieve logs from a stopped container.

```php
<?php

$manager->attach($container, $printCallback, true);
```

### Mapping a container's ports

You can map a container's private ports to the host's public ports using the `Docker\PortCollection` class.

```php
<?php

$ports = new Docker\PortCollection(80, 22);

$container = new Container();
$container->setExposedPorts($ports);

$manager
    ->create($container)
    ->start($container, ['PortBindings' => $ports->toSpec()]);
```

The `PortCollection` class understands the complete "Docker format" for specifying ports, which looks like this: `[[hostIp:][hostPort]:]port[/protocol]`. For example, all the following definitions are valid:

```
                            host ip   | host port | container port | protocol

0.0.0.0:2222:22/tcp     ->  0.0.0.0   | 2222      | 22             | tcp
127.0.0.1::22           ->  127.0.0.1 | 22        | 22             | tcp
5678/udp                ->            | 5678      | 5678           | udp
```

Once the container is running, you can retrive the mapped ports using the `getMappedPorts()` and `getMappedPort()` methods.

```php
<?php

$manager->start($container, $hostConfig);

$sshPort = $container->getMappedPort(22);
printf('SSH port is mapped to %d', $sshPort->getHostPort());

foreach ($container->getMappedPorts() as $mappedPort) {
    printf('Container\'s %d port is mapped to %d', $port->getPort(), $port->getHostPort());
}
```


## Finding and inspecting Containers

You can either find all containers with the `findAll()` methods or lookup one particular container by its id using `find()`.

```php
<?php

$manager = $docker->getContainerManager();

foreach ($manager->findAll() as $container) {
    // $container is an instance of Docker\Container
}

$id = retrieve_some_container_id_somehow();
$container = $manager->find($id);
```

If you just have the id of a container, you can use the `inspect()` method to retrieve more informations about it, like its exit code if relevant.

```php
$container = new Container();
$container->setId($someContainerId);

$manager->inspect($container);

printf('Container "%s" exited with code "%d"', $container->getId(), $container->getExitCode());
```

## Stopping and removing containers

You can stop and remove containers using the `stop()` and `remove()` methods respectively.

```php
<?php

$manager
    ->stop($container)
    ->remove($container);
```

## The Docker\Container class

The `Docker\Container` class is designed to help you manipulate containers. It has a few helper methods to set common runtime options.

```php
<?php

$container = new Container();
$container->setImage('ubuntu:precise');
$container->setMemory(1024*1024*128);
$container->setEnv(['SYMFONY_ENV=prod', 'FOO=bar']);
$container->setCmd(['/bin/echo', 'Hello Docker!']);

$manager->run($container);


printf('Container\'s id is %s', $container->getId());
printf('Container\'s name is $s', $container->getName();
printf('Container\'s exit code is %d', $container->getExitCode());
```

### Configuring exposed ports

Use the `Docker\PortCollection` class to manage exposed ports:

```php
<?php

$ports = new Docker\PortCollection(80, 22);
$ports->add(42);

$container->setExposedPorts($ports);
```
