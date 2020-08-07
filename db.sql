-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `todo_list`;
CREATE TABLE `todo_list`
(
    `id`       int(10) unsigned    NOT NULL AUTO_INCREMENT,
    `name`     text                NOT NULL,
    `user_id`  int(10) unsigned    NOT NULL,
    `archived` tinyint(1) unsigned NOT NULL,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `todo_list_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `todo_list_item`;
CREATE TABLE `todo_list_item`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `list_id`    int(10) unsigned NOT NULL,
    `text`       text             NOT NULL,
    `created_at` datetime         NOT NULL,
    `done`       tinyint(1)       NOT NULL,
    PRIMARY KEY (`id`),
    KEY `list_id` (`list_id`),
    CONSTRAINT `todo_list_item_ibfk_1` FOREIGN KEY (`list_id`) REFERENCES `todo_list` (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;


DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`
(
    `id`         int(10) unsigned NOT NULL AUTO_INCREMENT,
    `email`      varchar(255)     NOT NULL,
    `password`   varchar(64)      NOT NULL,
    `created_at` datetime         NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;