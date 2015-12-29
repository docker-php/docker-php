<?php

require __DIR__ . '/vendor/autoload.php';

$janeSwagger = \Joli\Jane\Swagger\JaneSwagger::build();
$files = $janeSwagger->generate(__DIR__ . '/docker-swagger.json', 'Docker\\API', __DIR__ . '/generated');
$janeSwagger->printFiles($files, __DIR__ . '/generated');

