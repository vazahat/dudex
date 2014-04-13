<?php
OW::getPluginManager()->addPluginSettingsRouteName('yncontactimporter', 'yncontactimporter-admin');
$config = OW::getConfig();

if ( !$config->configExists('yncontactimporter', 'contact_per_page') )
{
    $config->addConfig('yncontactimporter', 'contact_per_page', 30, 'Contacts Per Page');
}

if ( !$config->configExists('yncontactimporter', 'max_invite_per_times') )
{
    $config->addConfig('yncontactimporter', 'max_invite_per_times', 30, 'Maximum Invitation Per Times');
}

if ( !$config->configExists('yncontactimporter', 'default_invite_message') )
{
    $config->addConfig('yncontactimporter', 'default_invite_message', 'You are being invited to join our social network.', 'Default invite message');
}
if ( !$config->configExists('yncontactimporter', 'logo_width') )
{
    $config->addConfig('yncontactimporter', 'logo_width', 30, 'Widget logo width');
}

if ( !$config->configExists('yncontactimporter', 'logo_height') )
{
    $config->addConfig('yncontactimporter', 'logo_height', 30, 'Widget logo height');
}


$dbPrefix = OW_DB_PREFIX;

$sql =
    <<<EOT

CREATE TABLE IF NOT EXISTS `{$dbPrefix}yncontactimporter_provider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `title` varchar(20) NOT NULL,
  `enable` int(2) NOT NULL default '1',
  `order` int(2) NOT NULL default '200',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `{$dbPrefix}yncontactimporter_provider` (`name`, `title`, `enable`, `order`) VALUES
('facebook', 'Facebook', 1, 1),
('twitter', 'Twitter',  1, 2),
('linkedin', 'LinkedIn', 1, 3),
('gmail', 'GMail', 1, 4),
('hotmail', 'Live/Hotmail', 1, 5),
('yahoo', 'Yahoo', 1,  6),
('file CSV', 'File CSV/VCF', 1,  9);

CREATE TABLE IF NOT EXISTS `{$dbPrefix}yncontactimporter_pending` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL default '0',
  `emailId` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

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

EOT;

OW::getDbo()->query($sql);

$authorization = OW::getAuthorization();
$groupName = 'yncontactimporter';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'invite');

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('yncontactimporter')->getRootDir() . 'langs.zip', 'yncontactimporter');