<?php

Updater::getConfigService()->addConfig("mcompose", "friends_enabled", 1);
Updater::getConfigService()->addConfig("mcompose", "groups_enabled", 1);
Updater::getConfigService()->addConfig("mcompose", "events_enabled", 1);

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'mcompose');
