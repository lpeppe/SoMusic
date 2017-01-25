<?php
$sql = 'DROP TABLE '. OW_DB_PREFIX . 'somusic;';
OW::getDbo()->query($sql);
$sql = 'DROP TABLE '. OW_DB_PREFIX . 'somusic_post;';
OW::getDbo()->query($sql);