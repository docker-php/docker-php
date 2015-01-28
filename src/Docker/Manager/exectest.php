#!/usr/bin/php
<?php

#chdir(dirname(__DIR__));
chdir('/var/www/html/sites/all/libraries/composer/');
require_once 'autoload.php';

$client = new Docker\Http\DockerClient(array(), 'unix:////tmp/socat.sock'); # /var/run/docker.sock
$docker = new Docker\Docker($client);
$manager = $docker->getContainerManager();

try {
  $lookfor='vanilla2';
  $container = $manager->find($lookfor);
  $foo = $manager->exec($container);
#print_r($foo);

} catch (Exception $e) {
  echo $e->getMessage();
  #print_r($e->getResponse()->info);
  #print_r($e->getResponse()->__toString());
  
  #echo $e->getRequest();
  #if ($e->hasResponse()) {
  #  print_r($e->getResponse()->getStatusCode() . ' ' . $e->getResponse()->getReasonPhrase());
  # echo $e->getResponse();
  #}
}

print_r("\n");



