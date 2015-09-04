# Dealing with containers

## Running a container

Use `Docker\Manager\ContainerManager#run()` to run a container.

```php
<?php

$container = new Docker\Container(['Image' => 'ubuntu:precise']);
$docker->getContainerManager()->run($container);
```

The `run()` method provides a few options to handle the workflow of your container.

### Streaming a running container's output

You can automatically attach a created container and read its output by using a callback.
The callback function receives two arguments: the type of stream, and a piece of content. The type can be either `0`, `1` or `2`, denoting respectively `stdin`, `stdout` and `stderr` (as per [Docker's Attach multiplexing protocol](http://docs.docker.io/en/latest/reference/api/docker_remote_api_v1.9/#attach-to-a-container)).

```php
<?php

$manager = $docker->getContainerManager();
$manager->run($container, function($output, $type) {
    fputs($type === 1 ? STDOUT : STDERR, $output);
});
```
### Running a container as a daemon

You can run a container as a daemon (effectively making the `run()` method non-blocking) by passing `true` as its fourth argument.

```php
<?php

$manager->run($container, $callback, [], true);
```

## Creating, starting, attaching and waiting for containers

The `run()` command is actually a composite of `create()`, `start()`, `attach` and `wait`, just like the `docker run` CLI command that you might be used to. You can use these methods to gain more fine-grained control over your containers' workflow.

It's important to attach before starting the container, otherwise you may miss some commands output.

```php
<?php

$container = new Container(['Image' => 'ubuntu:precise']);

$manager = $docker->getContainerManager();
$manager->create($container);

printf('Created container with Id "%s"', $container->getId());

$manager->attach($container)->getBody()->readWithCallBack(function($output, $type) {
    print($output);
});

$manager->start($container);
$manager->wait($container);
```

The `attach()` method can also retrieve logs from a stopped container.

```php
<?php

$manager->attach($container, true)->getBody()->__toString();
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

Once the container is running, you can retrieve the mapped ports using the `getMappedPorts()` and `getMappedPort()` methods.

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

The `remove` method has a second argument (defaults to `false`) `$volumes` which allows you to remove the volumes associated with the container by setting it to `true`.


```php
$manager->remove($container, true);
```


## Removing multiple containers

You can remove multiple containers at once by passing an array of `Docker\Container` instances or strings (container id or container name) to the `removeContainers()` method.

```php
$manager->removeContainers([$container, '889ceddbb88e', 'angry_goodall']);
```

Same as the `remove` method it has a second argument (defaults to `false`) `$volumes` which allows you to remove the volumes associated with the containers by setting it to `true`.

Keep in mind that all of the containers have to be stopped before they can be removed.


## Copy files or folders from a container

Files and folders can be downloaded as a tar file from the container. The `copy()` function allows a folder/file to be downloaded as tar stream and `copyToDisk()` saves the result as a tar file.
In the following example the `/etc/default` folder from an Ubuntu container called vanilla is downloaded to `/tmp/file.tar`.

```php
<?php

$container = $manager->find('vanilla');
$manager->copyToDisk($container, '/etc/default', '/tmp/file.tar');
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
printf('Container\'s name is $s', $container->getName());
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

## Rename: Changing the human readable name of a container

Rename the container called vanilla1 to vanilla2:
```php
<?php

$container = $manager->find('vanilla1');
$manager->rename($container,'vanilla2');
```


## Exec: Run a process within an existing running container.

Running a process inside a running container is done in two steps: create an exec instance (identified by a hash value) and starting that instance (and then reading the returned data).
Example: connect to the container called 'vanilla2', create an exec for 'ls /var/www/html' (within a bash shell) and run it:

```php
<?php

$containerName = 'vanilla2';
$container = $manager->find($containerName);
$execid = $manager->exec($container, ["/bin/bash", "-c", "ls /var/www/html"]);
$response = $manager->execstart($execid);

print_r("Result= <" . $response->getBody()->__toString() . ">\n");
```

Note that after an exec has been created it can be run several times, e.g. an exec for '/bin/date' would return a different value each time execstart() is called.

You can also stream the result by using a callback during the execstart call :

```php
<?php

$logger = new Psr\Log();

$containerName = 'vanilla2';
$container = $manager->find($containerName);
$execid = $manager->exec($container, ["/bin/bash", "-c", "ls /var/www/html"]);

$response = $manager->execstart($execid, function ($log, $type) use($logger) {
	// Log output in real time
	$logger->info($log, array('type' => $type));
});

//Response stream is never read you need to simulate a wait in order to get output
$response->getBody()->getContents();
```
