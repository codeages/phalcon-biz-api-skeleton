<?php

define('ROOT_DIR', dirname(__DIR__));

$loader = require dirname(__DIR__).'/vendor/autoload.php';

\Doctrine\Common\Annotations\AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

\Codeages\Biz\Framework\Utility\Env::load(require ROOT_DIR.'/env.testing.php');

chdir(ROOT_DIR);

exec_command('IN_TESTING=true bin/phpmig migrate');

function exec_command($command)
{
    echo "[exec] {$command}\n";
    passthru($command);
}
