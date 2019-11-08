<?php

use Pimple\Psr11\Container as PsrContainer;

define('ROOT_DIR', dirname(__DIR__));

$biz = require __DIR__.'/biz.php';

$options = require ROOT_DIR. '/config/plumber.php';

$biz['biz'] = $biz;
$container = new PsrContainer($biz);

return [
    'options' => $options,
    'container' => $container,
];