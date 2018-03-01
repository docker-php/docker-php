# Basic Usage

`Docker\Docker` API Client offers all endpoints available for your version of Docker. Each endpoint has a strong PHPDoc
documentation in its comment, so the best way to know what values to set for an endpoint and what it returns is to go
directly to the endpoint documentation in the code.

As an example for listing container you can do:

```php
<?php
use Docker\Docker;

$docker = Docker::create();
$containers = $docker->containerList();

foreach ($containers as $container) {
    var_dump($container->getNames());
}
```
