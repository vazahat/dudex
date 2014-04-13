<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

OW::getPluginManager()->addPluginSettingsRouteName('profileprogressbar', 'profileprogressbar.admin');

$config = OW::getConfig();

if ( !$config->configExists('profileprogressbar', 'theme') )
{
    $config->addConfig( 'profileprogressbar', 'theme', 'standart' );
}

if ( !$config->configExists('profileprogressbar', 'features') )
{
    $config->addConfig('profileprogressbar', 'features', '{"friends":1,"photo":1,"video":1}');
}

if ( !$config->configExists('profileprogressbar', 'per_day') )
{
    $config->addConfig('profileprogressbar', 'per_day', 7);
}

if ( !$config->configExists('profileprogressbar', 'show_hint') )
{
    $config->addConfig('profileprogressbar', 'show_hint', 1);
}

OW::getDbo()->query('DROP TABLE IF EXISTS `' . OW_DB_PREFIX . 'profileprogressbar_activity_log`;
CREATE TABLE `' . OW_DB_PREFIX . 'profileprogressbar_activity_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(10) unsigned NOT NULL,
  `entityType` enum("blog-post", "event", "forum-topic", "friend_add", "group", "link", "photo_comments", "video_comments", "user_gift") NOT NULL,
  `timeStamp` int(10) unsigned NOT NULL,
  `entityId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `indexAll` (`userId`,`entityType`,`timeStamp`, `entityId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('profileprogressbar')->getRootDir() . 'langs.zip', 'profileprogressbar');

OW_ViewRenderer::getInstance()->clearCompiledTpl();
