<?php

/**
 * Copyright (c) 2013, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

OW::getDbo()->query( 'CREATE TABLE IF NOT EXISTS `' . OW_DB_PREFIX . 'antibruteforce_block_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` bigint(20) unsigned NOT NULL,
  `time` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

$config = OW::getConfig();

if ( !$config->configExists('antibruteforce', 'try_count') )
{
    $config->addConfig( 'antibruteforce', 'try_count', 5 );
};

if ( !$config->configExists('antibruteforce', 'expire_time') )
{
    $config->addConfig( 'antibruteforce', 'expire_time', 15 );
}

if ( !$config->configExists('antibruteforce', 'authentication') )
{
    $config->addConfig( 'antibruteforce', 'authentication', true );
}

if ( !$config->configExists('antibruteforce', 'registration') )
{
    $config->addConfig( 'antibruteforce', 'registration', true );
}

if ( !$config->configExists('antibruteforce', 'lock_title') )
{
    $config->addConfig( 'antibruteforce', 'lock_title', 'Anti Brute Force' );
}

if ( !$config->configExists('antibruteforce', 'lock_desc') )
{
    $config->addConfig( 'antibruteforce', 'lock_desc', 'Our protection system blocked your IP-address' );
}

OW::getPluginManager()->addPluginSettingsRouteName( 'antibruteforce', 'antibruteforce.admin');
