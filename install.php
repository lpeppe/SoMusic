<?php
$sql = 'CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'somusic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_owner` int(11) NOT NULL,
  `data` mediumtext NOT NULL,
  `title` varchar(255),
  `description` varchar(255),
  `timestamp_c` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `timestamp_m` TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'somusic_post` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `id_melody` int(11) NOT NULL,
    `id_post` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    )ENGINE=MyISAM  DEFAULT CHARSET=utf8;';

OW::getDbo()->query($sql);
