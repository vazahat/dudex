<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

$config = OW::getConfig();

if ( !$config->configExists('ocsaffiliates', 'allow_banners') )
{
    $config->addConfig('ocsaffiliates', 'allow_banners', '1', 'Allow uploading custom banners');
}

if ( !$config->configExists('ocsaffiliates', 'terms_agreement') )
{
    $config->addConfig('ocsaffiliates', 'terms_agreement', '0', 'Enable terms agreement');
}

try
{
    $query = "ALTER TABLE  `" . OW_DB_PREFIX . "ocsaffiliates_affiliate` ADD  `userId` INT NULL DEFAULT NULL;";
    Updater::getDbo()->query($query);
}
catch ( Exception $ex ) { }

try
{
    $query = "ALTER TABLE  `" . OW_DB_PREFIX . "ocsaffiliates_payout` ADD  `method` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  'currency';";
    Updater::getDbo()->query($query);
}
catch ( Exception $ex ) { }

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__).DS.'langs.zip', 'ocsaffiliates');