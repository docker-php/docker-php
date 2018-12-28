# Building an Image

In order to build an image you need to provide the `$inputStream` variable which correspond to the tar binary of a 
folder containing a `Dockerfile` (or another name by using the `dockerfile` parameters) and other files used during the
build.

Since `Docker` build directory can be heavy, Docker PHP override this call and allows passing a `resource` or a 
`Psr\Http\Message\StreamInterface` instance (string is also possible but not recommended). 
This avoid using too much memory in PHP.

## Build return

This function can return 3 different objects depending on the value of the `$fetch` parameter:
 
### Docker::FETCH_OBJECT

This is default mode, where this function will block until the build is finished and return an array of `BuildInfo` 
object.

This object contains the log of the build:

```php
<?php

use Docker\Docker;

$inputStream = create_tar_stream_resource();
$docker = Docker::create();

$buildStream = $docker->imageBuild($inputStream);
$buildStream->onFrame(function (BuildInfo $buildInfo) {
    echo $buildInfo->getStream();
});

$buildStream->wait();
```

### Docker::FETCH_RESPONSE

The build function will return the raw [PSR7](http://www.php-fig.org/psr/psr-7/) Response. It's up to you to handle 
decoding and receiving correct output in this case.

## Context

Docker PHP provides a `ContextInterface` and a default `Context` object for creating the `$inputStream` of the build
method.

```php
<?php

use Docker\Context\Context;
use Docker\Docker;

$context = new Context('/path/to/my/docker/build');
$inputStream = $context->toStream();
$docker = Docker::create();

$docker->imageBuild($inputStream);
```

You can safely use this context object to build image with a huge directory to size without consuming any memory or disk
on the PHP side as it will directly pipe the output of a `tar` process into the Docker Remote API.

### Context Builder

Additionally you can use the `ContextBuilder` to have a dynamic generation of your `Dockerfile`:

```php
<?php

use Docker\Context\ContextBuilder;
use Docker\Docker;

$contextBuilder = new ContextBuilder();
$contextBuilder->from('ubuntu:latest');
$contextBuilder->run('apt-get update && apt-get install -y php5');

$docker = Docker::create();
$docker->imageBuild($contextBuilder->getContext()->toStream());
```

