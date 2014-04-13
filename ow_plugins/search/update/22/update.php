<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

/*
$config = OW::getConfig();
if ( !$config->configExists('search', 'turn_offplugin_map') ){
    $config->addConfig('search', 'turn_offplugin_map', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_wiki') ){
    $config->addConfig('search', 'turn_offplugin_wiki', "1", '');
}
if ( !$config->configExists('search', 'allow_ads_adsense') ){
    $config->addConfig('search', 'allow_ads_adsense', "1", '');
}
if ( !$config->configExists('search', 'allow_ads_adspro') ){
    $config->addConfig('search', 'allow_ads_adspro', "0", '');
}
if ( !$config->configExists('search', 'allow_ads_ads') ){
    $config->addConfig('search', 'allow_ads_ads', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_news') ){
    $config->addConfig('search', 'turn_offplugin_news', "0", '');
}

*/

