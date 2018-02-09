<?php
define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR.'/vendor/autoload.php';

$app = require ROOT_DIR.'/bootstrap/web.php';

$app->handle();
