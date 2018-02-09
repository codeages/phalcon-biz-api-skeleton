<?php

use Codeages\PhalconBiz\Application;

$biz = require __DIR__.'/biz.php';

$config = require ROOT_DIR.'/config/web.php';
$app = new Application($biz, $config);

return $app;
