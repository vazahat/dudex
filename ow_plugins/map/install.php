<?php

/***
 * This software is intended for use with Oxwall Free Community Software
 * http://www.oxwall.org/ and is a proprietary licensed product.
 * For more information see License.txt in the plugin folder.

 * =============================================================================
 * Copyright (c) 2012 by Aron. All rights reserved.
 * =============================================================================


 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.
 * Pass on to others in any form are not permitted provided.
 * Sale are not permitted provided.
 * Sale this product are not permitted provided.
 * Gift this product are not permitted provided.
 * This plugin should be bought from the developer by paying money to PayPal account: biuro@grafnet.pl
 * Legal purchase is possible only on the web page URL: http://www.oxwall.org/store
 * Modyfing of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Modifying source code, all information like:copyright must remain.
 * Official website only: http://oxwall.a6.pl
 * Full license available at: http://oxwall.a6.pl


 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***/


//----databases:

//$sql = "TRUNCATE TABLE  `".$dbPref."map`";
$sql = "DROP TABLE IF EXISTS `".OW_DB_PREFIX."map`";
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map` (
  `id` int(11) NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `id_cat` int(10) NOT NULL default '0',
  `from_plugin` varchar(64) collate utf8_bin NOT NULL default 'map',
  `active` enum('0','1') collate utf8_bin NOT NULL default '1',
  `tags` varchar(255) collate utf8_bin default NULL,
  `type_promo` enum('normal','promotion_todate','promotion_unlimited') collate utf8_bin NOT NULL default 'normal',
  `name` varchar(100) collate utf8_bin NOT NULL,
  `desc` text collate utf8_bin NOT NULL,
  `slogan` varchar(64) collate utf8_bin default NULL,
  `discount` varchar(32) collate utf8_bin default NULL,
  `price` decimal(10,2) default NULL,
  `lat` varchar(128) collate utf8_bin NOT NULL,
  `lon` varchar(128) collate utf8_bin NOT NULL,
  `zoom` int(5) NOT NULL default '8',
  `ico` varchar(64) collate utf8_bin NOT NULL default 'world.png',
  `city_name` varchar(120) collate utf8_bin default NULL,
  `data_addm` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `last_modyfy` int(11) NOT NULL,
  `last_import` int(11) NOT NULL,
  `count_vived` bigint(22) NOT NULL default '0',
  `count_clicked` bigint(22) NOT NULL default '0',
  `oryginal_id` varchar(100) collate utf8_bin NOT NULL,
  `oryginal_furl` varchar(128) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_owner_2` (`id_owner`,`lat`,`lon`),
  KEY `active` (`active`),
  KEY `id_owner` (`id_owner`),
  KEY `from_plugin` (`from_plugin`),
  KEY `id_cat` (`id_cat`),
  KEY `tags` (`tags`),
  KEY `type_promo` (`type_promo`),
  KEY `price` (`price`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
OW::getDbo()->query($sql);



$sql = "DROP TABLE IF EXISTS `".OW_DB_PREFIX."map_images`";
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_images` (
  `idm` int(22) unsigned NOT NULL auto_increment,
  `id_ownerm` int(11) NOT NULL,
  `id_map` int(11) NOT NULL,
  `is_default` enum('0','1') collate utf8_bin NOT NULL default '1',
  `image` varchar(200) collate utf8_bin NOT NULL,
  `itype` varchar(10) collate utf8_bin NOT NULL default 'jpg',
  `data_add` timestamp NOT NULL default CURRENT_TIMESTAMP,
  UNIQUE KEY `idm` (`idm`),
  KEY `id_map` (`id_map`),
  KEY `id_ownerm` (`id_ownerm`),
  KEY `is_default` (`is_default`),
  KEY `data_add` (`data_add`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
OW::getDbo()->query($sql);

$sql = "DROP TABLE IF EXISTS `".OW_DB_PREFIX."map_category`";
OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_category` (
  `id` int(10) NOT NULL auto_increment,
  `id2` int(10) NOT NULL default '0',
  `active` enum('0','1') collate utf8_bin NOT NULL default '1',
  `name` varchar(100) collate utf8_bin NOT NULL,
  `name_translate` varchar(100) collate utf8_bin NOT NULL,
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`),
  KEY `id2` (`id2`),
  KEY `active` (`active`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;";
OW::getDbo()->query($sql);

$sql="CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_scan` (
  `id` int(11) NOT NULL auto_increment,
  `id_owner` int(11) NOT NULL,
  `active` enum('0','1') collate utf8_bin NOT NULL default '1',
  `secret` varchar(64) collate utf8_bin NOT NULL,
  `hash_unique` varchar(32) collate utf8_bin NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_owner` (`id_owner`),
  KEY `hash_unique` (`hash_unique`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=5 ;";
OW::getDbo()->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_scan_data` (
  `id_scan` int(11) NOT NULL,
  `id_owner` int(11) NOT NULL,
  `source_post_type` enum('online','file') collate utf8_bin NOT NULL default 'online',
  `add_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `d_latitude` varchar(25) collate utf8_bin NOT NULL,
  `d_longitude` varchar(25) collate utf8_bin NOT NULL,
  `d_accuracy` varchar(10) collate utf8_bin default NULL,
  `d_altitude` varchar(20) collate utf8_bin default NULL,
  `d_provider` varchar(10) collate utf8_bin default NULL,
  `d_bearing` varchar(10) collate utf8_bin default NULL,
  `d_speed` varchar(10) collate utf8_bin default NULL,
  `d_time` timestamp NULL default NULL,
  `d_battlevel` int(10) default NULL,
  `d_charging` int(2) default NULL,
  `d_deviceid` varchar(32) collate utf8_bin default NULL,
  `d_subscriberid` varchar(32) collate utf8_bin default NULL,
  `duplicate_times` int(11) NOT NULL default '0',
  UNIQUE KEY `allaaall` (`id_scan`,`id_owner`,`d_time`),
  KEY `id_scan` (`id_scan`),
  KEY `d_time` (`d_time`),
  KEY `id_owner` (`id_owner`),
  KEY `add_timestamp` (`add_timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
OW::getDbo()->query($sql);



$sql = "INSERT INTO  `".OW_DB_PREFIX."map_category` (
`id` ,
`id2` ,
`active` ,
`name` ,
`name_translate`
)
VALUES (
'',  '0',  '1',  'Default',  'cat_default'
);";
OW::getDbo()->query($sql);


$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."map_home` (
  `idh_owner` int(11) NOT NULL,
  `home_lat` varchar(128) collate utf8_bin NOT NULL,
  `home_lon` varchar(128) collate utf8_bin NOT NULL,
  UNIQUE KEY `idh_owner` (`idh_owner`),
  KEY `idh_owner_2` (`idh_owner`),
  KEY `home_lat` (`home_lat`),
  KEY `home_lon` (`home_lon`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
OW::getDbo()->query($sql);



$config = OW::getConfig();
if ( !$config->configExists('map', 'perpage') ){
    $config->addConfig('map', 'perpage', '300', '');
}
if ( !$config->configExists('map', 'tabdisable_fanpage') ){
    $config->addConfig('map', 'tabdisable_fanpage', '0', '');
}
if ( !$config->configExists('map', 'tabdisable_shop') ){
    $config->addConfig('map', 'tabdisable_shop', '0', '');
}
if ( !$config->configExists('map', 'tabdisable_events') ){
    $config->addConfig('map', 'tabdisable_events', '0', '');
}
if ( !$config->configExists('map', 'show_owner') ){
    $config->addConfig('map', 'show_owner', '1', '');
}
if ( !$config->configExists('map', 'tabdisable_news') ){
    $config->addConfig('map', 'tabdisable_news', '0', '');
}

if ( !$config->configExists('map', 'support_mobile_app') ){
    $config->addConfig('map', 'support_mobile_app', '0', '');
}



//----main:
BOL_LanguageService::getInstance()->addPrefix('map', 'Map');
OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('map')->getRootDir().'langs.zip', 'map');
OW::getPluginManager()->addPluginSettingsRouteName('map', 'map.admin');

