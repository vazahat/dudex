<?php
BOL_LanguageService::getInstance()->addPrefix('ynmediaimporter', 'Media Importer'); 

$config = OW::getConfig();

//Provider settings
if ( !$config->configExists('ynmediaimporter', 'enable_facebook') )
{
    $config->addConfig('ynmediaimporter', 'enable_facebook', 1, 'Enable Facebook provider');
}

if ( !$config->configExists('ynmediaimporter', 'enable_picasa') )
{
    $config->addConfig('ynmediaimporter', 'enable_picasa', 1, 'Enable Picasa provider');
}

if ( !$config->configExists('ynmediaimporter', 'enable_flickr') )
{
    $config->addConfig('ynmediaimporter', 'enable_flickr', 1, 'Enable Flickr provider');
}

if ( !$config->configExists('ynmediaimporter', 'enable_instagram') )
{
    $config->addConfig('ynmediaimporter', 'enable_instagram', 1, 'Enable Instagram provider');
}

/*
if ( !$config->configExists('ynmediaimporter', 'enable_yfrog') )
{
    $config->addConfig('ynmediaimporter', 'enable_yfrog', 1, 'Enable Yfrog provider');
}
*/

//General Settings
//1. Number Photos/Albums Per Page - How many photos/albums will be shown per page? (Enter a number between 10 and 40). Default 20
if ( !$config->configExists('ynmediaimporter', 'page') )
{
    $config->addConfig('ynmediaimporter', 'page', 20, 'Number Photos/Albums Per Page');
}

//2. Album Max Thumbnail Width - Enter a number between 100 and 200. Default: 160
if ( !$config->configExists('ynmediaimporter', 'album_thumb_width') )
{
    $config->addConfig('ynmediaimporter', 'album_thumb_width', 160, 'Album Max Thumbnail Width');
}

//3. Album Max Thumbnail Height - Enter a number between 100 and 200. Default: 116
if ( !$config->configExists('ynmediaimporter', 'album_thumb_height') )
{
    $config->addConfig('ynmediaimporter', 'album_thumb_height', 116, 'Album Max Thumbnail Height');
}

//4. Album Thumbnail Wrapper Height - Enter a number between 150 and 300. Default: 200
if ( !$config->configExists('ynmediaimporter', 'album_wrap_height') )
{
    $config->addConfig('ynmediaimporter', 'album_wrap_height', 200, 'Album Thumbnail Wrapper Height');
}

//5. Album Thumbnail Wrapper Margin - Enter a number between 5 and 20. Default: 10
if ( !$config->configExists('ynmediaimporter', 'album_wrap_margin') )
{
    $config->addConfig('ynmediaimporter', 'album_wrap_margin', 10, 'Album Thumbnail Wrapper Margin');
}

//6. Photo Max Thumbnail Width - Enter a number between 100 and 200. Default: 160
if ( !$config->configExists('ynmediaimporter', 'photo_thumb_width') )
{
    $config->addConfig('ynmediaimporter', 'photo_thumb_width', 160, 'Photo Max Thumbnail Width');
}

//7. Photo Max Thumbnail Height - Enter a number between 100 and 200. Default: 116
if ( !$config->configExists('ynmediaimporter', 'photo_thumb_height') )
{
    $config->addConfig('ynmediaimporter', 'photo_thumb_height', 116, 'Photo Max Thumbnail Height');
}

//8. Photo Thumbnail Wrapper Height - Enter a number between 150 and 300. Default: 180
if ( !$config->configExists('ynmediaimporter', 'photo_wrap_height') )
{
    $config->addConfig('ynmediaimporter', 'photo_wrap_height', 160, 'Photo Thumbnail Wrapper Height');
}

//9. Photo Thumbnail Wrapper Margin - Enter a number between 5 and 20. Default: 10
if ( !$config->configExists('ynmediaimporter', 'photo_wrap_margin') )
{
    $config->addConfig('ynmediaimporter', 'photo_wrap_margin', 10, 'Photo Thumbnail Wrapper Margin');
}

//10. Number Photos Per Queue - How many photos will be imported per each queue? (Enter a number between 10 and 100), suggest 20
if ( !$config->configExists('ynmediaimporter', 'number_photo') )
{
    $config->addConfig('ynmediaimporter', 'number_photo', 20, 'Number Photos Per Queue');
}

//11. Number Queue Per Cron - How many queue will be process per cron? (Enter a number between 10 and 200), suggest 20
if ( !$config->configExists('ynmediaimporter', 'number_queue') )
{
    $config->addConfig('ynmediaimporter', 'number_queue', 20, 'Number Queue Per Cron');
}

$dbPref = OW_DB_PREFIX;

//node table
$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."ynmediaimporter_nodes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `nid` varchar(64) NOT NULL,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `user_aid` int(10) unsigned NOT NULL DEFAULT '0',
  `scheduler_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_type` varchar(32) NOT NULL DEFAULT 'user',
  `key` varchar(64) NOT NULL,
  `uid` varchar(64) NOT NULL,
  `aid` varchar(64) NOT NULL,
  `media` varchar(32) NOT NULL,
  `provider` varchar(32) NOT NULL,
  `photo_count` varchar(32) NOT NULL,
  `status` smallint(8) unsigned NOT NULL DEFAULT '0',
  `title` varchar(256) NOT NULL,
  `src_thumb` tinytext,
  `src_small` tinytext,
  `src_medium` tinytext,
  `src_big` tinytext,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

//schedule table
$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."ynmediaimporter_schedulers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` smallint(5) unsigned NOT NULL DEFAULT '0',
  `last_run` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_id` int(11) unsigned NOT NULL DEFAULT '0',
  `owner_type` varchar(32) NOT NULL DEFAULT 'user',
  `params` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$path = OW::getPluginManager()->getPlugin('ynmediaimporter')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'ynmediaimporter');

OW::getPluginManager()->addPluginSettingsRouteName('ynmediaimporter', 'ynmediaimporter.admin_general');