<?php
	Updater::getDbo()->query("CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."toplink_children` (
		`id` int(11) NOT NULL auto_increment,
		`childof` int(11) NOT NULL,
		`name` varchar(255) NOT NULL,
		`url` tinytext NOT NULL,
		PRIMARY KEY(`id`)
	) ENGINE=MyISAM  DEFAULT CHARSET=utf8;");
	
	UTIL_File::removeDir( BOL_LanguageService::getImportDirPath().'toplink' );
	Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'toplink');
?>