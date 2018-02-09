<?php

if ('true' === getenv('IN_TESTING')) {
    \Codeages\Biz\Framework\Utility\Env::load(require ROOT_DIR.'/env.testing.php');
} else {
    \Codeages\Biz\Framework\Utility\Env::load(require ROOT_DIR.'/env.php');
}

ini_set('log_errors', 1);
if (env('DEBUG', false)) {
    error_reporting(E_ALL);
    ini_set('display_startup_errors', 1);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_startup_errors', 0);
    ini_set('display_errors', 0);
}

$config = require ROOT_DIR.'/config/biz.php';

$biz = new \App\Biz\AppBiz($config);
$biz->boot();

return $biz;
