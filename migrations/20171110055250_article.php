<?php

use Phpmig\Migration\Migration;

class Article extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $this->getContainer()['db']->exec("
        CREATE TABLE `article`(
            `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            `title` VARCHAR(1024) NOT NULL,
            `content` TEXT NOT NULL,
            `user_id` BIGINT(20) UNSIGNED NOT NULL,
            `is_recommended` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
            `created_at` INT(10) UNSIGNED NOT NULL,
            `updated_at` INT(10) UNSIGNED NOT NULL,
            PRIMARY KEY(`id`),
            KEY `username`(`user_id`)
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
