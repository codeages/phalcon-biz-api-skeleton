<?php

use Codeages\Biz\Framework\Testing\DbTestHelper;

if (!defined('ROOT_DIR')) {
    define('ROOT_DIR', dirname(dirname(__DIR__)));
}

require_once ROOT_DIR.'/bootstrap/bootstrap_test.php';

$config = require ROOT_DIR.'/config/biz.php';

$helper = new DbTestHelper($config['db.options']);
$helper->truncateAllTables();
