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
 * @package ow.ow_plugins.ocs_sitestats
 * @since 1.5
 */

$path = OW::getPluginManager()->getPlugin('ocssitestats')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'ocssitestats');

OW::getPluginManager()->addPluginSettingsRouteName('ocssitestats', 'ocssitestats.admin_config');

$config = OW::getConfig();

if ( !$config->configExists('ocssitestats', 'metrics') )
{
	$def = array(
		'total_users' => 1, 'online_users' => 1, 'new_users_today' => 1, 'new_users_this_month' => 1,
		'photos' => 1, 'videos' => 1, 'blogs' => 1, 'groups' => 1, 'events' => 1,
		'discussions' => 1, 'links' => 1
	);
    $config->addConfig('ocssitestats', 'metrics', json_encode($def), 'Metrics configuration');
}

if ( !$config->configExists('ocssitestats', 'zero_values') )
{
    $config->addConfig('ocssitestats', 'zero_values', 1, 'Show zero values');
}