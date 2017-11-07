<?php

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(dirname(__DIR__)));
}

require_once ROOT_DIR.'/bootstrap/bootstrap_test.php';

$config = require ROOT_DIR.'/config/biz.php';
$helper = new Codeages\PhalconBiz\DbTestHelper($config['db.options']);
$helper->truncateAllTables();
