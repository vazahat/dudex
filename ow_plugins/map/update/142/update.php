<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'map');

/*
$config = OW::getConfig();
if ( !$config->configExists('map', 'support_mobile_app') ){
    $config->addConfig('map', 'support_mobile_app', '0', '');
}
*/
/*
$errors=array();
try {
$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_scan` (
  `id` int(11) NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `active` enum('0','1') collate utf8_bin NOT NULL default '1',
  `secret` varchar(64) collate utf8_bin NOT NULL,
  `hash_unique` varchar(32) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `hash_unique` (`hash_unique`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}

try {
$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_scan_data` (
  `id_scan` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `source_post_type` enum('online','file') collate utf8_bin NOT NULL default 'online',
  `d_latitude` varchar(20) collate utf8_bin NOT NULL,
  `d_longitude` varchar(20) collate utf8_bin NOT NULL,
  `d_accuracy` varchar(10) collate utf8_bin default NULL,
  `d_altitude` varchar(20) collate utf8_bin default NULL,
  `d_provider` varchar(10) collate utf8_bin default NULL,
  `d_bearing` varchar(10) collate utf8_bin default NULL,
  `d_speed` varchar(10) collate utf8_bin default NULL,
  `d_time` timestamp NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `d_battlevel` int(10) default NULL,
  `d_charging` int(2) default NULL,
  `d_deviceid` varchar(32) collate utf8_bin default NULL,
  `d_subscriberid` varchar(32) collate utf8_bin default NULL,
  `duplicate_times` int(11) NOT NULL default '0',
  UNIQUE KEY `allaaall` (`id_scan`,`d_latitude`,`d_longitude`,`d_altitude`,`id_owner`),
  KEY `id_scan` (`id_scan`),
  KEY `d_time` (`d_time`),
  KEY `id_owner` (`id_owner`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
    Updater::getDbo()->query($sql);
} 
catch ( Exception $e ) 
{ 
    $errors[] = $e;
}


if ( !empty($errors) )
{
//    print_r($errors);
}
*/



