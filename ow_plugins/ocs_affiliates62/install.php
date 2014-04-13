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
 * @package ow.ow_plugins.ocs_affiliates
 * @since 1.5.3
 */
 
$config = OW::getConfig();

if ( !$config->configExists('ocsaffiliates', 'period') )
{
    $config->addConfig('ocsaffiliates', 'period', 3, 'Affiliate timeout');
}

if ( !$config->configExists('ocsaffiliates', 'click_amount') )
{
    $config->addConfig('ocsaffiliates', 'click_amount', 0.05, 'Amount per click');
}

if ( !$config->configExists('ocsaffiliates', 'reg_amount') )
{
    $config->addConfig('ocsaffiliates', 'reg_amount', 0.1, 'Amount per registration');
}

if ( !$config->configExists('ocsaffiliates', 'sale_commission') )
{
    $config->addConfig('ocsaffiliates', 'sale_commission', 'percent', 'Sale commission type');
}

if ( !$config->configExists('ocsaffiliates', 'sale_amount') )
{
    $config->addConfig('ocsaffiliates', 'sale_amount', 1, 'Amount per sale');
}

if ( !$config->configExists('ocsaffiliates', 'sale_percent') )
{
    $config->addConfig('ocsaffiliates', 'sale_percent', 5, 'Percent per sale');
}

if ( !$config->configExists('ocsaffiliates', 'signup_status') )
{
    $config->addConfig('ocsaffiliates', 'signup_status', 'active', 'New affiliate status');
}

if ( !$config->configExists('ocsaffiliates', 'show_rates') )
{
    $config->addConfig('ocsaffiliates', 'show_rates', '1', 'Show commission rate');
}

if ( !$config->configExists('ocsaffiliates', 'allow_banners') )
{
    $config->addConfig('ocsaffiliates', 'allow_banners', '1', 'Allow uploading custom banners');
}

if ( !$config->configExists('ocsaffiliates', 'terms_agreement') )
{
    $config->addConfig('ocsaffiliates', 'terms_agreement', '0', 'Enable terms agreement');
}

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_affiliate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(128) NOT NULL,
  `paymentDetails` text,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `emailVerified` tinyint(1) NOT NULL DEFAULT '0',
  `registerStamp` int(11) NOT NULL,
  `activityStamp` int(11) NOT NULL,
  `joinIp` int(10) unsigned NOT NULL,
  `userId` INT NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `registerStamp` (`registerStamp`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_affiliate_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `timestamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`),
  KEY `userId` (`userId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_banner` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `ext` varchar(5) NOT NULL,
  `uploadDate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_click` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `bonusAmount` decimal(9,2) NOT NULL,
  `clickDate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_payout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `amount` decimal(9,2) NOT NULL,
  `paymentDate` int(11) NOT NULL,
  `method` VARCHAR( 50 ) NULL DEFAULT 'currency',
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_reset_password` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `code` varchar(32) NOT NULL,
  `expirationTimeStamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `saleId` int(11) NOT NULL,
  `saleAmount` decimal(9,2) NOT NULL,
  `bonusAmount` decimal(9,2) NOT NULL,
  `saleDate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`),
  KEY `saleId` (`saleId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_signup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `bonusAmount` decimal(9,2) NOT NULL,
  `signupDate` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_verification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `affiliateId` int(11) NOT NULL,
  `code` varchar(100) NOT NULL,
  `startStamp` int(11) NOT NULL,
  `expireStamp` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliateId` (`affiliateId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "ocsaffiliates_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ipAddress` int(10) unsigned NOT NULL,
  `timestamp` int(11) NOT NULL,
  `type` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);


OW::getPluginManager()->addPluginSettingsRouteName('ocsaffiliates', 'ocsaffiliates.admin');

$path = OW::getPluginManager()->getPlugin('ocsaffiliates')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'ocsaffiliates');