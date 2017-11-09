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
                `access_key` VARCHAR(128) NOT NULL,
                `secret_key` VARCHAR(128) NOT NULL,
                `is_banned` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
                `created_at` INT(10) UNSIGNED NOT NULL,
                `updated_at` INT(10) UNSIGNED NOT NULL,
                PRIMARY KEY(`id`),
                UNIQUE KEY `username`(`username`)
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
