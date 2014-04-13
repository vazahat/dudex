<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

$config = OW::getConfig();
if ( !$config->configExists('search', 'search_force_users') ){
    $config->addConfig('search', 'search_force_users', "0", '');
}

