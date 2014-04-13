<?php

$path = OW::getPluginManager()->getPlugin('cacheextreme')->getRootDir() . 'langs.zip';
BOL_LanguageService::getInstance()->importPrefixFromZip($path, 'cacheextreme');

OW::getPluginManager()->addPluginSettingsRouteName('cacheextreme', 'cacheextreme.admin');

OW::getConfig()->addConfig('cacheextreme', 'template_cache', true);
OW::getConfig()->addConfig('cacheextreme', 'backend_cache', true);
OW::getConfig()->addConfig('cacheextreme', 'theme_static', true);
OW::getConfig()->addConfig('cacheextreme', 'plugin_static', true);