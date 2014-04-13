<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

$config = OW::getConfig();

if ( !$config->configExists('membership', 'subscribe_hidden_actions') )
{
    $config->addConfig('membership', 'subscribe_hidden_actions', '[]', 'Actions hidden on subscribe page');
}

$dbPref = OW_DB_PREFIX;

$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."membership_plan` (
  `id` int(11) NOT NULL auto_increment,
  `typeId` int(11) NOT NULL,
  `price` float(9,3) NOT NULL,
  `period` int(11) NOT NULL,
  `recurring` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `typeId` (`typeId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."membership_type` (
  `id` int(11) NOT NULL auto_increment,
  `roleId` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `roleId` (`roleId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);

$sql = "CREATE TABLE IF NOT EXISTS `".$dbPref."membership_user` (
  `id` int(11) NOT NULL auto_increment,
  `typeId` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `expirationStamp` int(11) NOT NULL,
  `recurring` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

OW::getDbo()->query($sql);


$product = new BOL_BillingProduct();
$product->active = 1;
$product->productKey = 'membership_plan';
$product->adapterClassName = 'MEMBERSHIP_CLASS_MembershipPlanProductAdapter';

BOL_BillingService::getInstance()->saveProduct($product);

OW::getPluginManager()->addPluginSettingsRouteName('membership', 'membership_admin');

$path = OW::getPluginManager()->getPlugin('membership')->getRootDir() . 'langs.zip';
OW::getLanguage()->importPluginLangs($path, 'membership');

$authorization = OW::getAuthorization();
$groupName = 'membership';
$authorization->addGroup($groupName, false);
