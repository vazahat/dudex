<?php
OW::getPluginManager()->addPluginSettingsRouteName('ynsocialbridge', 'ynsocialbridge-admin');

$dbPrefix = OW_DB_PREFIX;

$sql =
    <<<EOT

CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialbridge_apisetting` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `apiName` varchar(50) NOT NULL,
  `apiParams` text NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;
    
CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialbridge_token` (
  `id` int(11) NOT NULL auto_increment,
  `accessToken` varchar(256) collate utf8_unicode_ci default NULL,
  `secretToken` varchar(256) collate utf8_unicode_ci default NULL,
  `userId` int(11) unsigned NOT NULL default '0',
  `service` varchar(32) collate utf8_unicode_ci NOT NULL,
  `uid` varchar(64) collate utf8_unicode_ci NOT NULL,
  `timestamp`  INT( 10 ) NOT NULL,
  PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialbridge_queue` (
  `id` int(11) unsigned NOT NULL auto_increment,
  `tokenId`  int(11) NOT NULL,
  `userId` int(11) unsigned NOT NULL default '0',
  `service` varchar(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `extraParams` text collate utf8_unicode_ci NOT NULL,
  `lastRun` int(11) NOT NULL,
  `nextRun` int(11) NULL,
  `priority` tinyint(1) NULL,
  `errorId` int(11) NULL,
  `errorMessage` text collate utf8_unicode_ci NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
)ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `{$dbPrefix}ynsocialbridge_statistic` (
  `id` int(11) NOT NULL auto_increment,
  `service` varchar(32) NOT NULL,
  `userId` int(11) unsigned NOT NULL default '0',
  `uid` varchar(64) collate utf8_unicode_ci NOT NULL,
  `inviteOfDay` int(20) unsigned NOT NULL default '0',
  `date` date NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;

EOT;

OW::getDbo()->query($sql);

$authorization = OW::getAuthorization();
$groupName = 'ynsocialbridge';
$authorization->addGroup($groupName);

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('ynsocialbridge')->getRootDir() . 'langs.zip', 'ynsocialbridge');