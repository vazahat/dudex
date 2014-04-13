<?php

Updater::getConfigService()->deleteConfig('equestions', 'live_notifications');
Updater::getConfigService()->deleteConfig('equestions', 'live_notifications_period');

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'equestions');