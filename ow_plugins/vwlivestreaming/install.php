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

$config = OW::getConfig();

if ( !$config->configExists('vwls', 'videos_per_page') )
{
    $config->addConfig('vwls', 'videos_per_page', 20, 'Videos per page');
}

if ( !$config->configExists('vwls', 'enableRTMP') )
{
    $config->addConfig('vwls', 'enableRTMP', 1, 'enableRTMP');
}

if ( !$config->configExists('vwls', 'enableP2P') )
{
    $config->addConfig('vwls', 'enableP2P', 0, 'enableP2P');
}

if ( !$config->configExists('vwls', 'supportRTMP') )
{
    $config->addConfig('vwls', 'supportRTMP', 1, 'supportRTMP');
}

if ( !$config->configExists('vwls', 'supportP2P') )
{
    $config->addConfig('vwls', 'supportP2P', 0, 'supportP2P');
}

if ( !$config->configExists('vwls', 'alwaysRTMP') )
{
    $config->addConfig('vwls', 'alwaysRTMP', 0, 'alwaysRTMP');
}

if ( !$config->configExists('vwls', 'alwaysP2P') )
{
    $config->addConfig('vwls', 'alwaysP2P', 0, 'alwaysP2P');
}

if ( !$config->configExists('vwls', 'videoCodec') )
{
    $config->addConfig('vwls', 'videoCodec', 'H264', 'videoCodec');
}

if ( !$config->configExists('vwls', 'codecProfile') )
{
    $config->addConfig('vwls', 'codecProfile', 'main', 'codecProfile');
}

if ( !$config->configExists('vwls', 'codecLevel') )
{
    $config->addConfig('vwls', 'codecLevel', '3.1', 'codecLevel');
}

if ( !$config->configExists('vwls', 'soundCodec') )
{
    $config->addConfig('vwls', 'soundCodec', 'Speex', 'soundCodec');
}

if ( !$config->configExists('vwls', 'server') )
{
    $config->addConfig('vwls', 'server', 'rtmp://localhost/videowhisper', 'RTMP server');
}

if ( !$config->configExists('vwls', 'serverAMF') )
{
    $config->addConfig('vwls', 'serverAMF', 'AMF3', 'RTMP AMF');
}

if ( !$config->configExists('vwls', 'serverRTMFP') )
{
    $config->addConfig('vwls', 'serverRTMFP', 'rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/', 'RTMFP server');
}

if ( !$config->configExists('vwls', 'p2pGroup') )
{
    $config->addConfig('vwls', 'p2pGroup', 'VideoWhisper', 'P2P Group');
}

if ( !$config->configExists('vwls', 'tokenKey') )
{
    $config->addConfig('vwls', 'tokenKey', 'VideoWhisper', 'Set tokenKey parameters');
}

if ( !$config->configExists('vwls', 'snapshotsTime') )
{
    $config->addConfig('vwls', 'snapshotsTime', '60000', 'Snapshots time');
}

if ( !$config->configExists('vwls', 'camMaxBandwidth') )
{
    $config->addConfig('vwls', 'camMaxBandwidth', '81920', 'Maximum Bandwidth');
}

if ( !$config->configExists('vwls', 'bufferLive') )
{
    $config->addConfig('vwls', 'bufferLive', '0.1', 'Set bufferLive broadcasting parameters');
}

if ( !$config->configExists('vwls', 'bufferFull') )
{
    $config->addConfig('vwls', 'bufferFull', '0.1', 'Set bufferFull broadcasting parameters');
}

if ( !$config->configExists('vwls', 'bufferLive2') )
{
    $config->addConfig('vwls', 'bufferLive2', '0.1', 'Set bufferLive video/watch parameters');
}

if ( !$config->configExists('vwls', 'bufferFull2') )
{
    $config->addConfig('vwls', 'bufferFull2', '0.1', 'Set bufferFull video/watch parameters');
}

if ( !$config->configExists('vwls', 'disableBandwidthDetection') )
{
    $config->addConfig('vwls', 'disableBandwidthDetection', 0, 'disableBandwidthDetection');
}

if ( !$config->configExists('vwls', 'limitByBandwidth') )
{
    $config->addConfig('vwls', 'limitByBandwidth', 1, 'limitByBandwidth');
}

if ( !$config->configExists('vwls', 'generateSnapshots') )
{
    $config->addConfig('vwls', 'generateSnapshots', 1, 'Enable sending jpg webcam snapshots to server');
}

if ( !$config->configExists('vwls', 'externalInterval') )
{
    $config->addConfig('vwls', 'externalInterval', 5000, 'Set externalInterval broadcasting. Set 0 or any number lower than 500 to disable');
}

if ( !$config->configExists('vwls', 'externalInterval2') )
{
    $config->addConfig('vwls', 'externalInterval2', 5000, 'Set externalInterval video/watch. Set 0 or any number lower than 500 to disable');
}

if ( !$config->configExists('vwls', 'ws_ads') )
{
    $config->addConfig('vwls', 'ws_ads', 'ads.php', 'ws_ads');
}

if ( !$config->configExists('vwls', 'adsTimeout') )
{
    $config->addConfig('vwls', 'adsTimeout', 15000, 'adsTimeout to setup time in milliseconds until first ad is shown');
}

if ( !$config->configExists('vwls', 'adsInterval') )
{
    $config->addConfig('vwls', 'adsInterval', 240000, 'ads Interval');
}

if ( !$config->configExists('vwls', 'statusInterval') )
{
    $config->addConfig('vwls', 'statusInterval', 10000, 'status Interval');
}

if ( !$config->configExists('vwls', 'availability') )
{
    $config->addConfig('vwls', 'availability', 0, 'Availability');
}

if ( !$config->configExists('vwls', 'status') )
{
    $config->addConfig('vwls', 'status', 'approved', 'status');
}

if ( !$config->configExists('vwls', 'member') )
{
    $config->addConfig('vwls', 'member', 'all', 'member');
}

if ( !$config->configExists('vwls', 'member_list') )
{
    $config->addConfig('vwls', 'member_list', '', 'member_list');
}

$baseSwf_url = OW_URL_HOME.'ow_plugins/vwlivestreaming/ls/';
if ( !$config->configExists('vwls', 'baseSwf_url') )
{
    $config->addConfig('vwls', 'baseSwf_url', $baseSwf_url, 'baseSwf_url');
}

$dbPref = OW_DB_PREFIX;

// 'permission' consists of 
// showCamSettings:advancedCamSettings:configureSource:onlyVideo:noVideo:noEmbeds:showTimer:writeText:privateTextchat:
// fillWindow:writeText2:enableVideo:enableChat:enableUsers:fillWindow2:verboseLevel (16)
$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."vwls_clip` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL default '',
  `description` text NOT NULL,
  `modifDatetime` int(11) NOT NULL default '0',
  `roomLimit` int(11) NOT NULL,
  `welcome` text NOT NULL,
  `welcome2` text NOT NULL,
  `offlineMessage` text NOT NULL,
  `camWidth` int(11) NOT NULL,
  `camHeight` int(11) NOT NULL,
  `camFPS` int(11) NOT NULL,
  `micRate` int(11) NOT NULL,
  `soundQuality` int(11) NOT NULL,
  `camBandwidth` int(11) NOT NULL,
  `labelColor` varchar(50) NOT NULL,
  `layoutCode` text NOT NULL,
  `layoutCode2` text NOT NULL,
  `filterRegex` text NOT NULL,
  `filterReplace` text NOT NULL,
  `floodProtection` int(11) NOT NULL,
  `floodProtection2` int(11) NOT NULL,
  `permission` varchar(128) NOT NULL,
  `status` varchar(50) NOT NULL,
  `user_list` text NOT NULL,
  `moderator_list` text NOT NULL,
  `addDatetime` int(11) NOT NULL default '0',
  `privacy` varchar(50) NOT NULL default 'everybody',
  `online` varchar(128) NOT NULL default 'no',
  `onlineCount` int(11) NOT NULL,
  `onlineUser` varchar(128) NOT NULL default '',
  `onlineUsers` varchar(128) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

OW::getPluginManager()->addPluginSettingsRouteName('vwls', 'vwls_admin_config');

$authorization = OW::getAuthorization();
$groupName = 'vwls';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'add');
$authorization->addAction($groupName, 'view', true);
$authorization->addAction($groupName, 'add_comment');
$authorization->addAction($groupName, 'delete_comment_by_content_owner');

$path = OW::getPluginManager()->getPlugin('vwls')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'vwls');
