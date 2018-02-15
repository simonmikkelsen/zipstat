CREATE TABLE `zipstat`.`zs20_change_requests` ( `id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(255) NOT NULL , `token` VARCHAR(255) NOT NULL , `type` TINYTEXT NOT NULL , `expires` DATETIME NOT NULL , PRIMARY KEY (`id`), UNIQUE `uuid_index` (`uuid`)) ENGINE = InnoDB;

CREATE TABLE `zipstat`.`zs20_sessions` ( `token` VARCHAR(255) NOT NULL , `username` VARCHAR(64) NOT NULL , `permissions` VARCHAR(255) NOT NULL , `expires` DATETIME NOT NULL , UNIQUE (`token`)) ENGINE = InnoDB CHARSET=utf8 COLLATE utf8_bin;

ALTER TABLE `zs20_main` ADD `statsitePublic` BOOLEAN NOT NULL DEFAULT FALSE AFTER `hash`;
