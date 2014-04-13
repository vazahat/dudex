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
BOL_LanguageService::getInstance()->addPrefix('ivideo', 'Video Uploader');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('ivideo')->getRootDir() . 'langs.zip', 'ivideo');

OW::getPluginManager()->addPluginSettingsRouteName('ivideo', 'ivideo_admin');

$authorization = OW::getAuthorization();
$groupName = 'ivideo';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'add_comment');
$authorization->addAction($groupName, 'add');
$authorization->addAction($groupName, 'delete_comment_by_content_owner');
$authorization->addAction($groupName, 'view', true);

if (!OW::getConfig()->configExists('ivideo', 'allowedFileSize'))
    OW::getConfig()->addConfig('ivideo', 'allowedFileSize', '20', '');

if (!OW::getConfig()->configExists('ivideo', 'allowedExtensions'))
    OW::getConfig()->addConfig('ivideo', 'allowedExtensions', 'mp4,flv', '');

if (!OW::getConfig()->configExists('ivideo', 'videoWidth'))
    OW::getConfig()->addConfig('ivideo', 'videoWidth', '500', '');

if (!OW::getConfig()->configExists('ivideo', 'videoHeight'))
    OW::getConfig()->addConfig('ivideo', 'videoHeight', '400', '');

if (!OW::getConfig()->configExists('ivideo', 'videoPreviewWidth'))
    OW::getConfig()->addConfig('ivideo', 'videoPreviewWidth', '180', '');

if (!OW::getConfig()->configExists('ivideo', 'videoPreviewHeight'))
    OW::getConfig()->addConfig('ivideo', 'videoPreviewHeight', '150', '');

if (!OW::getConfig()->configExists('ivideo', 'resultsPerPage'))
    OW::getConfig()->addConfig('ivideo', 'resultsPerPage', '10', '');

if (!OW::getConfig()->configExists('ivideo', 'videoApproval'))
    OW::getConfig()->addConfig('ivideo', 'videoApproval', 'auto', '');

if (!OW::getConfig()->configExists('ivideo', 'theme'))
    OW::getConfig()->addConfig('ivideo', 'theme', 'classicTheme', '');

if (!OW::getConfig()->configExists('ivideo', 'videosPerRow'))
    OW::getConfig()->addConfig('ivideo', 'videosPerRow', '4', '');

if (!OW::getConfig()->configExists('ivideo', 'makeUploaderMain'))
    OW::getConfig()->addConfig('ivideo', 'makeUploaderMain', '0', '');

if (!OW::getConfig()->configExists('ivideo', 'ffmpegPath'))
    OW::getConfig()->addConfig('ivideo', 'ffmpegPath', '', '');

OW::getDbo()->query("
   CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ivideo_videos` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL default '',
  `description` text,
  `owner` int(11) NOT NULL,
  `filename` varchar(1000) NOT NULL,
  `status` enum('pending','approved') NOT NULL default 'approved',
  `privacy` varchar(50) NOT NULL default 'everybody',
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ivideo_videos_featured` (
  `id` int(11) NOT NULL auto_increment,
  `videoId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ivideo_categories` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(128) NOT NULL,
  `description` text,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ivideo_videos_category` (
  `id` int(11) NOT NULL auto_increment,
  `videoId` int(11) NOT NULL,
  `categoryId` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);