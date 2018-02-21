# HTTPlug bundle

If you are using [Symfony](http://symfony.com/)
with the [HTTPlug bundle](http://docs.php-http.org/en/latest/integrations/symfony-bundle.html),
you may want to setup the client to take benefit of the profiling, without have to re-configure all the stuff.

To do it, we have to call `Docker\DockerClient::createFormEnv` factory method from the HTTPlug factory.

First, declare the factory service:

```yaml
services:
    httplug.factory.docker:
        class: Http\HttplugBundle\Collector\ProfileClientFactory
        arguments:
            $factory: [ Docker\DockerClient, 'createFromEnv' ]
```

The `ProfileClientFactory` will detect and call the passed `DockerClient` factory method.

Then, declare the client on the HTTPlug bundle configuration:

```yaml
httplug:
    plugins:
        logger: ~
    clients:
        docker:
            factory: 'httplug.factory.docker'
            plugins: ['httplug.plugin.logger']
```

Finally, declare your `Docker\Docker` instance as a service, giving the HTTPlug client wrapper:

```yaml
services:
    Docker\Docker:
        arguments:
            $httpClient: '@httplug.client.docker'
```

And voilà! Just call your `Docker\Docker` service and you will get the same result with profiling and logs.
