# Installation

The recommended way to install Docker PHP is of course to use [Composer](http://getcomposer.org/):

Run `composer require docker-php/docker-php` to add the dependency

By default it will use the last API version. However you can specify the API version of docker by setting a specific
version for the ` docker-php/docker-php-api`.

To use the 1.29 version you can do the following:

```
composer require docker-php/docker-php-api:4.1.29.*
```

Do not use `^4.1.29.0` otherwise you will also depend on the lastest version. First digit of this version number match the 
major version of [Jane PHP](https://github.com/janephp/janephp) which is the lib generating the API Client code.

Also please not that, unfortunately some endpoints of the Docker API may have BC break during minor version. This library may
try to hide those BC break but it will always be a best effort. Feel free to raise an issue or pull request on github when
you encounter one.
