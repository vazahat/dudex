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

if ( !$config->configExists('vwvr', 'videos_per_page') )
{
    $config->addConfig('vwvr', 'videos_per_page', 20, 'Videos per page');
}

if ( !$config->configExists('vwvr', 'server') )
{
    $config->addConfig('vwvr', 'server', 'rtmp://localhost/videowhisper', 'RTMP server');
}

if ( !$config->configExists('vwvr', 'serverAMF') )
{
    $config->addConfig('vwvr', 'serverAMF', 'AMF3', 'RTMP AMF');
}

if ( !$config->configExists('vwvr', 'videoCodec') )
{
    $config->addConfig('vwvr', 'videoCodec', 'H264', 'videoCodec');
}

if ( !$config->configExists('vwvr', 'codecProfile') )
{
    $config->addConfig('vwvr', 'codecProfile', 'main', 'codecProfile');
}

if ( !$config->configExists('vwvr', 'codecLevel') )
{
    $config->addConfig('vwvr', 'codecLevel', '3.1', 'codecLevel');
}

if ( !$config->configExists('vwvr', 'soundCodec') )
{
    $config->addConfig('vwvr', 'soundCodec', 'Speex', 'soundCodec');
}

if ( !$config->configExists('vwvr', 'soundQuality') )
{
    $config->addConfig('vwvr', 'soundQuality', '9', 'soundQuality');
}

if ( !$config->configExists('vwvr', 'micRate') )
{
    $config->addConfig('vwvr', 'micRate', '22', 'micRate');
}

if ( !$config->configExists('vwvr', 'camMaxBandwidth') )
{
    $config->addConfig('vwvr', 'camMaxBandwidth', '131072', 'Maximum Bandwidth');
}

if ( !$config->configExists('vwvr', 'bufferLive') )
{
    $config->addConfig('vwvr', 'bufferLive', '900', 'Set bufferLive parameters');
}

if ( !$config->configExists('vwvr', 'bufferFull') )
{
    $config->addConfig('vwvr', 'bufferFull', '900', 'Set bufferFull parameters');
}

if ( !$config->configExists('vwvr', 'bufferLivePlayback') )
{
    $config->addConfig('vwvr', 'bufferLivePlayback', '0.2', 'Set bufferLivePlayback parameters');
}

if ( !$config->configExists('vwvr', 'bufferFullPlayback') )
{
    $config->addConfig('vwvr', 'bufferFullPlayback', '10', 'Set bufferFullPlayback parameters');
}

if ( !$config->configExists('vwvr', 'availability') )
{
    $config->addConfig('vwvr', 'availability', 0, 'Availability');
}

if ( !$config->configExists('vwvr', 'status') )
{
    $config->addConfig('vwvr', 'status', 'approved', 'status');
}

if ( !$config->configExists('vwvr', 'member') )
{
    $config->addConfig('vwvr', 'member', 'all', 'member');
}

if ( !$config->configExists('vwvr', 'member_list') )
{
    $config->addConfig('vwvr', 'member_list', '', 'member_list');
}

$baseSwf_url = OW_URL_HOME.'ow_plugins/vwvideorecorder/vr/';
if ( !$config->configExists('vwvr', 'baseSwf_url') )
{
    $config->addConfig('vwvr', 'baseSwf_url', $baseSwf_url, 'baseSwf_url');
}

// user setting
if ( !$config->configExists('vwvr', 'recordLimit') )
{
    $config->addConfig('vwvr', 'recordLimit', '600', 'recordLimit');
}

if ( !$config->configExists('vwvr', 'camWidth') )
{
    $config->addConfig('vwvr', 'camWidth', '320', 'camWidth');
}

if ( !$config->configExists('vwvr', 'camHeight') )
{
    $config->addConfig('vwvr', 'camHeight', '240', 'camHeight');
}

if ( !$config->configExists('vwvr', 'camFPS') )
{
    $config->addConfig('vwvr', 'camFPS', '10', 'camFPS');
}

if ( !$config->configExists('vwvr', 'camBandwidth') )
{
    $config->addConfig('vwvr', 'camBandwidth', '40960', 'camBandwidth');
}

if ( !$config->configExists('vwvr', 'layoutCode') )
{
    $config->addConfig('vwvr', 'layoutCode', 'id=0&label=Video&x=346&y=10&width=326&height=298; id=1&label=Camcorder&x=10&y=10&width=326&height=298', 'layoutCode');
}

if ( !$config->configExists('vwvr', 'showCamSettings') )
{
    $config->addConfig('vwvr', 'showCamSettings', '1', 'showCamSettings');
}

if ( !$config->configExists('vwvr', 'advancedCamSettings') )
{
    $config->addConfig('vwvr', 'advancedCamSettings', '0', 'advancedCamSettings');
}

if ( !$config->configExists('vwvr', 'fillWindow') )
{
    $config->addConfig('vwvr', 'fillWindow', '1', 'fillWindow');
}

$recordPath = OW_DIR_ROOT.'ow_plugins/vwvideorecorder/vr/recorded';
if ( !$config->configExists('vwvr', 'recordPath') )
{
    $config->addConfig('vwvr', 'recordPath', $recordPath, 'recordPath');
}


$dbPref = OW_DB_PREFIX;

$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."vwvr_clip` (
  `id` int(11) NOT NULL auto_increment,
  `userId` int(11) NOT NULL,
  `recordingId` varchar(50) NOT NULL,
  `title` varchar(128) NOT NULL default '',
  `room_name` varchar(128) NOT NULL default '',
  `description` text NOT NULL,
  `status` varchar(50) NOT NULL,
  `addDatetime` int(11) NOT NULL default '0',
  `privacy` varchar(50) NOT NULL default 'everybody',
  PRIMARY KEY  (`id`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

OW::getPluginManager()->addPluginSettingsRouteName('vwvr', 'vwvr_admin_config');

$authorization = OW::getAuthorization();
$groupName = 'vwvr';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'add');
$authorization->addAction($groupName, 'view', true);
$authorization->addAction($groupName, 'add_comment');
$authorization->addAction($groupName, 'delete_comment_by_content_owner');

$path = OW::getPluginManager()->getPlugin('vwvr')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'vwvr');
