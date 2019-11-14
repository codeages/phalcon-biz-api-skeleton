<?php
define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR.'/vendor/autoload.php';

$biz = require ROOT_DIR.'/bootstrap/biz.php';

$handler = new \App\JsonRpcServerHandler($biz);

$server = new \Datto\JsonRpc\Http\Server($handler);
$server->reply();

