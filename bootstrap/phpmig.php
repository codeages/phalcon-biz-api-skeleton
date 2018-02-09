<?php

$biz = require __DIR__.'/biz.php';

$migration = new \Codeages\Biz\Framework\Dao\MigrationBootstrap($biz['db'], $biz['migration.directories']);

return $migration->boot();
