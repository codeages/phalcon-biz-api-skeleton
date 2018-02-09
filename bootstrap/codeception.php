<?php

\Codeages\Biz\Framework\Utility\Env::load(require ROOT_DIR.'/env.testing.php');

chdir(ROOT_DIR);

exec_command('IN_TESTING=true bin/phpmig migrate');

function exec_command($command)
{
    echo "[exec] {$command}";
    passthru($command);
}
