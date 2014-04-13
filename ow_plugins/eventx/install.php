<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is a proprietary licensed product. 
 * For more information see License.txt in the plugin folder.

 * ---
 * Copyright (c) 2012, Purusothaman Ramanujam
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.

 * This plugin should be bought from the developer by paying money to PayPal account (purushoth.r@gmail.com).

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
OW::getPluginManager()->addPluginSettingsRouteName('eventx', 'eventx_admin_index');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('eventx')->getRootDir() . 'langs.zip', 'eventx');

if (!OW::getConfig()->configExists('eventx', 'eventDelete'))
    OW::getConfig()->addConfig('eventx', 'eventDelete', '1,2,3', '');

if (!OW::getConfig()->configExists('eventx', 'resultsPerPage'))
    OW::getConfig()->addConfig('eventx', 'resultsPerPage', '30', '');

if (!OW::getConfig()->configExists('eventx', 'itemApproval'))
    OW::getConfig()->addConfig('eventx', 'itemApproval', 'auto', '');

if (!OW::getConfig()->configExists('eventx', 'enableTagsList'))
    OW::getConfig()->addConfig('eventx', 'enableTagsList', "1", '');

if (!OW::getConfig()->configExists('eventx', 'enableCategoryList'))
    OW::getConfig()->addConfig('eventx', 'enableCategoryList', "1", '');

if (!OW::getConfig()->configExists('eventx', 'enableMultiCategories'))
    OW::getConfig()->addConfig('eventx', 'enableMultiCategories', "0", '');

if (!OW::getConfig()->configExists('eventx', 'enable3DTagCloud'))
    OW::getConfig()->addConfig('eventx', 'enable3DTagCloud', "1", '');

if (!OW::getConfig()->configExists('eventx', 'enableMapSuggestion'))
    OW::getConfig()->addConfig('eventx', 'enableMapSuggestion', "1", '');

if (!OW::getConfig()->configExists('eventx', 'mapWidth'))
    OW::getConfig()->addConfig('eventx', 'mapWidth', "200", '');

if (!OW::getConfig()->configExists('eventx', 'mapHeight'))
    OW::getConfig()->addConfig('eventx', 'mapHeight', "200", '');

if (!OW::getConfig()->configExists('eventx', 'enableCalendar'))
    OW::getConfig()->addConfig('eventx', 'enableCalendar', "1", '');

if (!OW::getConfig()->configExists('eventx', 'showPastEvents'))
    OW::getConfig()->addConfig('eventx', 'showPastEvents', '0', '');

if (!OW::getConfig()->configExists('eventx', 'eventsCount'))
    OW::getConfig()->addConfig('eventx', 'eventsCount', '50', '');

if (!OW::getConfig()->configExists('eventx', 'calendarHeight'))
    OW::getConfig()->addConfig('eventx', 'calendarHeight', '0', '');

if (!OW::getConfig()->configExists('eventx', 'openLinksType'))
    OW::getConfig()->addConfig('eventx', 'openLinksType', '1', '');

if (!OW::getConfig()->configExists('eventx', 'isRTLLanguage'))
    OW::getConfig()->addConfig('eventx', 'isRTLLanguage', '0', '');

if (!OW::getConfig()->configExists('eventx', 'showWeekends'))
    OW::getConfig()->addConfig('eventx', 'showWeekends', '1', '');

if (!OW::getConfig()->configExists('eventx', 'firstWeekDay'))
    OW::getConfig()->addConfig('eventx', 'firstWeekDay', '0', '');

OW::getDbo()->query("
   CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "eventx_item` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `location` text NOT NULL,
  `createTimeStamp` int(11) NOT NULL,
  `startTimeStamp` int(11) NOT NULL,
  `endTimeStamp` int(11) default NULL,
  `userId` int(11) NOT NULL,
  `whoCanView` tinyint(4) NOT NULL,
  `whoCanInvite` tinyint(4) NOT NULL,
  `maxInvites` int(11) NOT NULL,
  `status` enum('pending','approved') NOT NULL default 'approved',
  `image` VARCHAR(32) default NULL,
  `endDateFlag` BOOL NOT NULL DEFAULT '0',
  `startTimeDisabled` BOOL NOT NULL DEFAULT '0',
  `endTimeDisabled` BOOL NOT NULL DEFAULT '0',
  `importId` int(11) NOT NULL default '0',
  `importStatus` int(11) NOT NULL default '0',  
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("
  CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "eventx_invite` (
  `id` int(11) NOT NULL auto_increment,
  `eventId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `inviterId` int(11) NOT NULL,
  `displayInvitation` BOOL NOT NULL DEFAULT '1',
  `timeStamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `inviteUnique` (`userId`,`inviterId`,`eventId`),
  KEY `userId` (`userId`),
  KEY `inviterId` (`inviterId`),
  KEY `eventId` (`eventId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

OW::getDbo()->query("
   CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "eventx_user` (
  `id` int(11) NOT NULL auto_increment,
  `eventId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `timeStamp` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `eventUser` (`eventId`,`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("
  CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "eventx_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `description` text,
  `master` INT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getDbo()->query("
 CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "eventx_event_category` (
  `id` int(11) NOT NULL auto_increment,
  `eventId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

$authorization = OW::getAuthorization();
$groupName = 'eventx';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'add_event');
$authorization->addAction($groupName, 'view_event', true);
$authorization->addAction($groupName, 'add_comment');

OW::getDbo()->query("INSERT INTO `" . OW_DB_PREFIX . "eventx_categories` (`id`, `name`, `description`, `master`) VALUES(1, 'Default', 'Default Category',0);");
OW::getDbo()->query("INSERT INTO `" . OW_DB_PREFIX . "eventx_categories` (`id`, `name`, `description`, `master`) VALUES(2, 'General', 'Local Events',0);");
