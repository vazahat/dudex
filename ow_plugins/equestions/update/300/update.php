<?php

try
{
    Updater::getDbo()->query("ALTER TABLE `" . OW_DB_PREFIX . "equestions_notification` ADD `special` TINYINT NULL ,ADD INDEX ( `special` )");
}
catch ( Exception $e ) {}

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'equestions');