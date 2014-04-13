<?php

BOL_LanguageService::getInstance()->importPrefixFromZip( dirname(__FILE__) . DS . 'langs.zip', 'attachments');

OW::getConfig()->addConfig('attachments', 'photo_share', '1', 'Add attached photos to user photos');
OW::getConfig()->addConfig('attachments', 'video_share', '1', 'Add attached videos to user videos');
OW::getConfig()->addConfig('attachments', 'link_share', '1', 'Add attached links to user links');

OW::getPluginManager()->addPluginSettingsRouteName('attachments', 'attachments-settings-page');
