<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
$plugin = OW::getPluginManager()->getPlugin('utags');

$dbPrefix = OW_DB_PREFIX;

$sql = array();
$sql[] = "CREATE TABLE IF NOT EXISTS `{$dbPrefix}utags_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `entityType` varchar(100) DEFAULT NULL,
  `entityId` varchar(50) NOT NULL,
  `photoId` int(11) NOT NULL,
  `copyPhotoId` int(11) DEFAULT NULL,
  `timeStamp` int(11) NOT NULL,
  `status` varchar(50) CHARACTER SET utf8 NOT NULL DEFAULT 'approval',
  `data` text,
  PRIMARY KEY (`id`),
  KEY `userId` (`entityType`,`photoId`,`timeStamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

foreach ( $sql as $query )
{
    OW::getDbo()->query($query);
}

OW::getPluginManager()->addPluginSettingsRouteName('utags', 'utags-settings-page');

OW::getConfig()->addConfig('utags', 'copy_photo', 1, 'Copy photo');
OW::getConfig()->addConfig('utags', 'crop_photo', 1, 'Crop coppied photo preview');


$authorization = OW::getAuthorization();
$groupName = 'utags';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'view_tags', true);
$authorization->addAction($groupName, 'add_tags');


BOL_LanguageService::getInstance()->importPrefixFromZip($plugin->getRootDir() . 'langs.zip', 'utags');
