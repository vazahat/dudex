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

BOL_LanguageService::getInstance()->addPrefix('credits', 'Credits History and Transfer Credits');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('credits')->getRootDir().'langs.zip', 'credits');

OW::getPluginManager()->addPluginSettingsRouteName('credits', 'credits_admin');

if ( !OW::getConfig()->configExists('credits', 'logsPerPage') )
    OW::getConfig()->addConfig('credits', 'logsPerPage', '15', '');

if ( !OW::getConfig()->configExists('credits', 'enableEmail') )
    OW::getConfig()->addConfig('credits', 'enableEmail', '', '');
    
if ( !OW::getConfig()->configExists('credits', 'enablePM') )
    OW::getConfig()->addConfig('credits', 'enablePM', '', '');
    
if ( !OW::getConfig()->configExists('credits', 'enableNotification') )
    OW::getConfig()->addConfig('credits', 'enableNotification', '1', '');
            
OW::getDbo()->query("
   CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "credits_sent_log` (
  `id` int(11) NOT NULL auto_increment,
  `senderItem` int(11) NOT NULL,
  `receiverItem` int(11) NOT NULL,
  `sender` int(11) NOT NULL,
  `receiver` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

$authorization = OW::getAuthorization();
$groupName = 'credits';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'send');
$authorization->addAction($groupName, 'receive');