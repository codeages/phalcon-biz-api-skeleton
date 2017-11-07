<?php

use Codeages\PhalconBiz\Application;

$biz = require __DIR__.'/bootstrap_biz.php';

$config = require ROOT_DIR.'/config/app.php';
$app = new Application($biz, $config);

return $app;
