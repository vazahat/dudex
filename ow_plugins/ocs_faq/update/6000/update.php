<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
try
{
	$sql = "ALTER TABLE `".OW_DB_PREFIX."ocsfaq_question` ADD `categoryId` INT NULL DEFAULT NULL";

	Updater::getDbo()->query($sql);
}
catch ( Exception $ex ) { }

try
{
	$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."ocsfaq_category` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `name` varchar(100) NOT NULL,
	  `order` int(6) NOT NULL DEFAULT '0',
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

	Updater::getDbo()->query($sql);
}
catch ( Exception $ex ) { }


Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'ocsfaq');