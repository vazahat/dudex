<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo
 * @since 1.0
 */

$dbo = Updater::getDbo();
$sqlErrors = array();

$queries = array(
    "ALTER TABLE  `".OW_DB_PREFIX."oaseo_meta` ADD  `dispatchAttrs` TEXT NULL DEFAULT NULL",
    
    "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(100) NOT NULL,
  `data` mediumtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
    
    "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_sitemap_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `value` text NOT NULL,
  `addTs` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
    
    "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_sitemap_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `title` text,
  `meta` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `processTs` int(11) NOT NULL DEFAULT '0',
  `broken` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_index` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1",
    
    "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_sitemap_page_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageId` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pageId` (`pageId`),
  KEY `itemId` (`itemId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1"
    
);

foreach ( $queries as $query )
{
    try
    {
        $dbo->query($query);
    }
    catch( Exception $e )
    {
        $sqlErrors[] = $e;
    }
}

$config = Updater::getConfigService();

if( !$config->configExists('oaseo', 'crawler_lock') )
{
    $config->addConfig('oaseo', 'crawler_lock', 0);
}

if( !$config->configExists('oaseo', 'update_info') )
{
    $config->addConfig('oaseo', 'update_info', 0);
}

if( !$config->configExists('oaseo', 'update_maps') )
{
    $config->addConfig('oaseo', 'update_maps', 0);
}

if( !$config->configExists('oaseo', 'sitemap_url') )
{
    $config->addConfig('oaseo', 'sitemap_url', 'sitemap.xml');
}

if( !$config->configExists('oaseo', 'imagemap_url') )
{
    $config->addConfig('oaseo', 'imagemap_url', 'sitemap_images.xml');
}

if( !$config->configExists('oaseo', 'update_freq') )
{
    $config->addConfig('oaseo', 'update_freq', 604800);
}

if( !$config->configExists('oaseo', 'update_ts') )
{
    $config->addConfig('oaseo', 'update_ts', 0);
}

if( !$config->configExists('oaseo', 'inform') )
{
    $config->addConfig('oaseo', 'inform', '["google"]');
}

if( !$config->configExists('oaseo', 'sitemap_init') )
{
    $config->addConfig('oaseo', 'sitemap_init', 0);
}

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'oaseo');

