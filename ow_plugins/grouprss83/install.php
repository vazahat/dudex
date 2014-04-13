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

BOL_LanguageService::getInstance()->addPrefix('grouprss', 'RSS Auto Feeder for Groups');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('grouprss')->getRootDir().'langs.zip', 'grouprss');

OW::getPluginManager()->addPluginSettingsRouteName('grouprss', 'grouprss_admin');

if ( !OW::getConfig()->configExists('grouprss', 'actionMember') )
    OW::getConfig()->addConfig('grouprss', 'actionMember', 'both', '');

if ( !OW::getConfig()->configExists('grouprss', 'disablePosting') )
    OW::getConfig()->addConfig('grouprss', 'disablePosting', '', '');

if ( !OW::getConfig()->configExists('grouprss', 'postLocation') )
    OW::getConfig()->addConfig('grouprss', 'postLocation', 'newsfeed', '');
            
OW::getDbo()->query("
   CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "grouprss_feeds` (
  `id` int(11) NOT NULL auto_increment,
  `groupId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,  
  `feedUrl` varchar(255) NOT NULL,
  `feedCount` int(3) NOT NULL,  
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");