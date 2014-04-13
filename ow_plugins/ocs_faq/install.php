<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * /install.php
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq
 * @since 1.0
 */

$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."ocsfaq_question` (
 `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
 `question` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
 `answer` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL ,
 `isFeatured` TINYINT( 1 ) NOT NULL DEFAULT '0' , 
 `order` INT NOT NULL DEFAULT '0',
 `categoryId` INT NULL DEFAULT NULL
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".OW_DB_PREFIX."ocsfaq_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `order` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$path = OW::getPluginManager()->getPlugin('ocsfaq')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'ocsfaq');

OW::getPluginManager()->addPluginSettingsRouteName('ocsfaq', 'ocsfaq.admin_config');

$config = OW::getConfig();

if ( !$config->configExists('ocsfaq', 'expand_answers') )
{
    $config->addConfig('ocsfaq', 'expand_answers', 1, 'Show answers expanded');
}