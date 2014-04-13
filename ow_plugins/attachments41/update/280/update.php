<?php

try
{
    UPDATE_ConfigService::getInstance()->addConfig('attachments', 'photo_share', '1', 'Add attached photos to user photos');
    UPDATE_ConfigService::getInstance()->addConfig('attachments', 'video_share', '1', 'Add attached videos to user videos');
    UPDATE_ConfigService::getInstance()->addConfig('attachments', 'link_share', '1', 'Add attached links to user links');
}
catch ( Exception $e ) {}

try
{
    OW::getPluginManager()->addPluginSettingsRouteName('attachments', 'attachments-settings-page');
}
catch ( Exception $e ) {}

$updateDir = dirname(__FILE__) . DS;
Updater::getLanguageService()->importPrefixFromZip($updateDir . 'langs.zip', 'attachments');