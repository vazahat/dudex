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
 */

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `meta` text NOT NULL,
  `uri` varchar(255) NOT NULL,
  `dispatchAttrs` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`),
  KEY `uri` (`uri`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_slug` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entityType` varchar(50) NOT NULL,
  `entityId` int(11) NOT NULL,
  `string` varchar(200) NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `entry_unique` (`entityType`,`string`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `routeName` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `routeName` (`routeName`,`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_sitemap_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `value` text NOT NULL,
  `addTs` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_sitemap_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL,
  `title` text,
  `meta` text,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `processTs` int(11) NOT NULL DEFAULT '0',
  `broken` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_index` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");

OW::getDbo()->query("CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_sitemap_page_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pageId` int(11) NOT NULL,
  `itemId` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `pageId` (`pageId`),
  KEY `itemId` (`itemId`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1");


$autoAddedArray = array('blogs', 'forum', 'event');
$pluginManager = OW::getPluginManager();

foreach ( $autoAddedArray as $key => $pluginKey )
{
    if ( !$pluginManager->isPluginActive($pluginKey) )
    {
        unset($autoAddedArray[$key]);
    }
}

$config = OW::getConfig();
$config->addConfig('oaseo', 'slug_plugins', json_encode($autoAddedArray));
$config->addConfig('oaseo', 'slug_old_urls_enabled', 1);
$config->addConfig('oaseo', 'slug_filter_words', '[]');
$config->addConfig('oaseo', 'crawler_lock', 0);
$config->addConfig('oaseo', 'update_info', 0);
$config->addConfig('oaseo', 'update_maps', 0);
$config->addConfig('oaseo', 'sitemap_url', 'sitemap.xml');
$config->addConfig('oaseo', 'imagemap_url', 'sitemap_images.xml');
$config->addConfig('oaseo', 'update_freq', 604800);
$config->addConfig('oaseo', 'update_ts', 0);
$config->addConfig('oaseo', 'inform', '["google"]');
$config->addConfig('oaseo', 'sitemap_init', 0);

$robotsConfigContents = <<<EOT
User-agent: *

Disallow: ow_version.xml
Disallow: INSTALL.txt
Disallow: LICENSE.txt
Disallow: README.txt
Disallow: UPDATE.txt
Disallow: CHANGES.txt

Disallow: /admin/
Disallow: /forgot-password
Disallow: /sign-in
Disallow: /ajax-form
Disallow: /users/waiting-for-approval
Disallow: /join
Disallow: /profile/edit
Disallow: /email-verify
Disallow: /ow_updates/
EOT;

OW::getConfig()->addConfig('oaseo', 'robots_contents', $robotsConfigContents);

OW::getPluginManager()->addPluginSettingsRouteName('oaseo', 'oaseo.admin_index');
OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('oaseo')->getRootDir() . 'langs.zip', 'oaseo');

