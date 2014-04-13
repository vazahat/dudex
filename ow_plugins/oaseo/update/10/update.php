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

$dbo->query(
    "CREATE TABLE IF NOT EXISTS `" . OW_DB_PREFIX . "oaseo_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `routeName` varchar(50) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `routeName` (`routeName`,`url`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1"
);

if ( !Updater::getConfigService()->configExists('oaseo', 'robots_contents') )
{
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

    Updater::getConfigService()->addConfig('oaseo', 'robots_contents', $robotsConfigContents);
}

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'oaseo');