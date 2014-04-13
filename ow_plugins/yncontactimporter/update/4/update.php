<?php

// update languges
Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'yncontactimporter');

//Update database
$dbPrefix = OW_DB_PREFIX;

$sql =
    <<<EOT
CREATE TABLE IF NOT EXISTS `{$dbPrefix}yncontactimporter_invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `type` enum('email','social') NOT NULL,
  `provider` varchar(64) NOT NULL,
  `friendId` varchar(64) NOT NULL,
  `email` varchar(64) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'email or name',
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `isUsed` tinyint(1) NOT NULL DEFAULT '0',
  `sentTime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `{$dbPrefix}yncontactimporter_joined` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `inviterId` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `{$dbPrefix}yncontactimporter_statistic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `totalSent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

DELETE FROM `{$dbPrefix}yncontactimporter_provider` WHERE `{$dbPrefix}yncontactimporter_provider`.`name` = 'myspace';
DELETE FROM `{$dbPrefix}yncontactimporter_provider` WHERE `{$dbPrefix}yncontactimporter_provider`.`name` = 'mail2world';

EOT;

Updater::getDbo()->query($sql);