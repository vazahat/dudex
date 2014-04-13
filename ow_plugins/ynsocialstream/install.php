<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

$plugin = OW::getPluginManager()->getPlugin('ynsocialstream');
$dbPrefix = OW_DB_PREFIX;
$query="

CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialstream_feeds` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `uid` VARCHAR( 128 ) NOT NULL ,
  `userId` INT( 11 ) NOT NULL,
  `provider` varchar(128) NOT NULL, 
  `timestamp` varchar(128) NOT NULL, 
  `updateKey` varchar(128) NOT NULL, 
  `updateType` varchar(128) NOT NULL, 
  `creationDate` datetime  NULL,
  `modifiedDate` datetime  NULL,
  `photoUrl` varchar(255)  NULL, 
  `title` varchar(128)  NULL,
  `href` varchar(255)  NULL,
  `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci  NULL,
  `friendId` INT( 11 ) NOT NULL ,
  `friendName` varchar(128) NOT NULL,
  `friendHref` varchar(255)  NULL,
  `friendDescription` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `privacy` VARCHAR( 64 ) NULL,  
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
";

 OW::getDbo()->query($query);


//installing language pack
OW::getLanguage()->importPluginLangs($plugin->getRootDir().'langs.zip', 'ynsocialstream');
//adding admin settings page
OW::getPluginManager()->addPluginSettingsRouteName('ynsocialstream', 'ynsocialstream-global-settings');

//add configure back-end global setting
OW::getConfig()->addConfig('ynsocialstream', 'get_feed_cron', 0, 'Allow Cron Job');
OW::getConfig()->addConfig('ynsocialstream', 'max_facebook_get_feed', 5, 'Maximum Allowed Get Feed Per Times in Facebook');
OW::getConfig()->addConfig('ynsocialstream', 'max_twitter_get_feed', 5, 'Maximum Allowed Get Feed Per Times in Twitter');
OW::getConfig()->addConfig('ynsocialstream', 'max_linkedin_get_feed', 5, 'Maximum Allowed Get Feed Per Times in Linkedin');


//add privacy in back-end
$authorization = OW::getAuthorization();
$groupName = 'ynsocialstream';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'get_feed');





