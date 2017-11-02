<?php

$biz = require __DIR__.'/bootstrap_biz.php';

$migration = new \Codeages\Biz\Framework\Dao\MigrationBootstrap($biz['db'], $biz['migration.directories']);

return $migration->boot();
