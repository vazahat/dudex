<?php
/*
$plugin = OW::getPluginManager()->getPlugin('toplink');
BOL_LanguageService::getInstance()->importPrefixFromZip($plugin->getRootDir() . 'langs.zip', 'toplink');
*/

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."toplink_item` (
	`id` int(11) NOT NULL auto_increment,
	`itemname` varchar(255) NOT NULL,
	`url` tinytext NOT NULL,
	`icon` tinytext,
	`target` int(2),
	`order` int(3),
	PRIMARY KEY(`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."toplink_permission` (
	`id` int(11) NOT NULL auto_increment,
	`itemid` int(11) NOT NULL,
	`availablefor` int(3) NOT NULL,
	PRIMARY KEY(`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."toplink_children` (
	`id` int(11) NOT NULL auto_increment,
	`childof` int(11) NOT NULL,
	`name` varchar(255) NOT NULL,
	`url` tinytext NOT NULL,
	PRIMARY KEY(`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");

OW::getPluginManager()->addPluginSettingsRouteName('toplink', 'toplink.admin');

$authorization = OW::getAuthorization();
$groupName = 'toplink';
$authorization->addGroup($groupName);
$authorization->addAction($groupName, 'show_toplink', true);
$action = BOL_AuthorizationService::getInstance()->findAction('toplink', 'show_toplink');
BOL_AuthorizationPermissionDao::getInstance()->deleteByActionId($action->getId());

$path = OW::getPluginManager()->getPlugin('toplink')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'toplink');
?>