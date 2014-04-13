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

if ( !$config->configExists('vwvc', 'videos_per_page') )
{
    $config->addConfig('vwvc', 'videos_per_page', 20, 'Videos per page');
}

if ( !$config->configExists('vwvc', 'enableRTMP') )
{
    $config->addConfig('vwvc', 'enableRTMP', 1, 'enableRTMP');
}

if ( !$config->configExists('vwvc', 'enableP2P') )
{
    $config->addConfig('vwvc', 'enableP2P', 0, 'enableP2P');
}

if ( !$config->configExists('vwvc', 'supportRTMP') )
{
    $config->addConfig('vwvc', 'supportRTMP', 1, 'supportRTMP');
}

if ( !$config->configExists('vwvc', 'supportP2P') )
{
    $config->addConfig('vwvc', 'supportP2P', 0, 'supportP2P');
}

if ( !$config->configExists('vwvc', 'alwaysRTMP') )
{
    $config->addConfig('vwvc', 'alwaysRTMP', 0, 'alwaysRTMP');
}

if ( !$config->configExists('vwvc', 'alwaysP2P') )
{
    $config->addConfig('vwvc', 'alwaysP2P', 0, 'alwaysP2P');
}

if ( !$config->configExists('vwvc', 'videoCodec') )
{
    $config->addConfig('vwvc', 'videoCodec', 'H264', 'videoCodec');
}

if ( !$config->configExists('vwvc', 'codecProfile') )
{
    $config->addConfig('vwvc', 'codecProfile', 'main', 'codecProfile');
}

if ( !$config->configExists('vwvc', 'codecLevel') )
{
    $config->addConfig('vwvc', 'codecLevel', '3.1', 'codecLevel');
}

if ( !$config->configExists('vwvc', 'soundCodec') )
{
    $config->addConfig('vwvc', 'soundCodec', 'Speex', 'soundCodec');
}

if ( !$config->configExists('vwvc', 'server') )
{
    $config->addConfig('vwvc', 'server', 'rtmp://localhost/videowhisper', 'RTMP server');
}

if ( !$config->configExists('vwvc', 'serverAMF') )
{
    $config->addConfig('vwvc', 'serverAMF', 'AMF3', 'RTMP AMF');
}

if ( !$config->configExists('vwvc', 'serverRTMFP') )
{
    $config->addConfig('vwvc', 'serverRTMFP', 'rtmfp://stratus.adobe.com/f1533cc06e4de4b56399b10d-1a624022ff71/', 'RTMFP server');
}

if ( !$config->configExists('vwvc', 'p2pGroup') )
{
    $config->addConfig('vwvc', 'p2pGroup', 'VideoWhisper', 'P2P Group');
}

if ( !$config->configExists('vwvc', 'camMaxBandwidth') )
{
    $config->addConfig('vwvc', 'camMaxBandwidth', '81920', 'Maximum Bandwidth');
}

if ( !$config->configExists('vwvc', 'bufferLive') )
{
    $config->addConfig('vwvc', 'bufferLive', '0.1', 'Buffer Live');
}

if ( !$config->configExists('vwvc', 'bufferFull') )
{
    $config->addConfig('vwvc', 'bufferFull', '0.1', 'Buffer Full');
}

if ( !$config->configExists('vwvc', 'bufferLivePlayback') )
{
    $config->addConfig('vwvc', 'bufferLivePlayback', '0.1', 'Buffer Live Playback');
}

if ( !$config->configExists('vwvc', 'bufferFullPlayback') )
{
    $config->addConfig('vwvc', 'bufferFullPlayback', '0.1', 'Buffer Full Playback');
}

if ( !$config->configExists('vwvc', 'disableBandwidthDetection') )
{
    $config->addConfig('vwvc', 'disableBandwidthDetection', 0, 'disableBandwidthDetection');
}

if ( !$config->configExists('vwvc', 'disableUploadDetection') )
{
    $config->addConfig('vwvc', 'disableUploadDetection', 0, 'disableUploadDetection');
}

if ( !$config->configExists('vwvc', 'limitByBandwidth') )
{
    $config->addConfig('vwvc', 'limitByBandwidth', 1, 'limitByBandwidth');
}

if ( !$config->configExists('vwvc', 'ws_ads') )
{
    $config->addConfig('vwvc', 'ws_ads', 'ads.php', 'ws_ads');
}

if ( !$config->configExists('vwvc', 'adsTimeout') )
{
    $config->addConfig('vwvc', 'adsTimeout', 15000, 'ads Timeout');
}

if ( !$config->configExists('vwvc', 'adsInterval') )
{
    $config->addConfig('vwvc', 'adsInterval', 240000, 'ads Interval');
}

if ( !$config->configExists('vwvc', 'statusInterval') )
{
    $config->addConfig('vwvc', 'statusInterval', 10000, 'status Interval');
}

if ( !$config->configExists('vwvc', 'availability') )
{
    $config->addConfig('vwvc', 'availability', 0, 'Availability');
}

if ( !$config->configExists('vwvc', 'status') )
{
    $config->addConfig('vwvc', 'status', 'approved', 'status');
}

if ( !$config->configExists('vwvc', 'member') )
{
    $config->addConfig('vwvc', 'member', 'all', 'member');
}

if ( !$config->configExists('vwvc', 'member_list') )
{
    $config->addConfig('vwvc', 'member_list', '', 'member_list');
}

$baseSwf_url = OW_URL_HOME.'ow_plugins/vwvideoconference/vc/';
if ( !$config->configExists('vwvc', 'baseSwf_url') )
{
    $config->addConfig('vwvc', 'baseSwf_url', $baseSwf_url, 'baseSwf_url');
}

$dbPref = OW_DB_PREFIX;

// 'permission' consists of fillWindow:advancedCamSettings:
// showCamSettings:configureSource:disableVideo:disableSound:panelRooms:panelUsers:panelFiles:file_upload:file_delete:tutorial:
// autoViewCams:showTimer:writeText:regularWatch:newWatch:privateTextchat:administrator:verboseLevel (20)
$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."vwvc_clip` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) NOT NULL,
  `title` varchar(128) NOT NULL default '',
  `description` text NOT NULL,
  `modifDatetime` int(11) NOT NULL default '0',
  `welcome` text NOT NULL,
  `camWidth` int(11) NOT NULL,
  `camHeight` int(11) NOT NULL,
  `camFPS` int(11) NOT NULL,
  `micRate` int(11) NOT NULL,
  `soundQuality` int(11) NOT NULL,
  `camBandwidth` int(11) NOT NULL,
  `background_url` varchar(128) NOT NULL default '',
  `layoutCode` text NOT NULL,
  `filterRegex` text NOT NULL,
  `filterReplace` text NOT NULL,
  `floodProtection` int(11) NOT NULL,
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

OW::getPluginManager()->addPluginSettingsRouteName('vwvc', 'vwvc_admin_config');

$authorization = OW::getAuthorization();
$groupName = 'vwvc';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'add');
$authorization->addAction($groupName, 'view', true);
$authorization->addAction($groupName, 'add_comment');
$authorization->addAction($groupName, 'delete_comment_by_content_owner');

$path = OW::getPluginManager()->getPlugin('vwvc')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'vwvc');
