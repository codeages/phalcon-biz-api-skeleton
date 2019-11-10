<?php
define('ROOT_DIR', dirname(__DIR__));

require ROOT_DIR.'/vendor/autoload.php';

$biz = require ROOT_DIR.'/bootstrap/biz.php';
$biz->boot();

return $biz;