ALTER TABLE phpbb_users ADD COLUMN credits varchar(255) DEFAULT '0';

CREATE TABLE IF NOT EXISTS `categories` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `servers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `ip` text CHARACTER SET utf8 NOT NULL,
    `type` int(11) NOT NULL,
    `players` int(11) NOT NULL,
    `maxplayers` int(11) NOT NULL,
    `map` varchar(255) CHARACTER SET utf8 NOT NULL,
    `os` varchar(255) CHARACTER SET utf8 NOT NULL,
    `name` varchar(255) CHARACTER SET utf8 NOT NULL,
    `vip` int(1) NOT NULL,
    `startvip` int(11) DEFAULT NULL,
    `expirevip` int(11) DEFAULT NULL,
    `added` int(11) NOT NULL,
    `owner` int(11) NOT NULL,
    `cache` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;