<?php

use Phpmig\Migration\Migration;

class Init extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->getContainer()['db']->exec("
            CREATE TABLE `user`(
                `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(32) NOT NULL,
                `email` VARCHAR(128) NOT NULL,
                `password` VARCHAR(128) NOT NULL,
                `salt` VARCHAR(64) NOT NULL,
                `created_at` INT(10) UNSIGNED NOT NULL,
                `updated_at` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY(`id`),
                UNIQUE KEY `username`(`username`),
                UNIQUE KEY `email`(`email`)
            ) ENGINE = InnoDB DEFAULT CHARSET = utf8;
        ");
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $this->getContainer()['db']->exec("DROP TABLE `user`");
    }
}
