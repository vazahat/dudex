<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

$config = OW::getConfig();
if ( !$config->configExists('search', 'turn_offplugin_cms') ){
    $config->addConfig('search', 'turn_offplugin_cms', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_forum') ){
    $config->addConfig('search', 'turn_offplugin_forum', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_links') ){
    $config->addConfig('search', 'turn_offplugin_links', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_video') ){
    $config->addConfig('search', 'turn_offplugin_video', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_photo') ){
    $config->addConfig('search', 'turn_offplugin_photo', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_shoppro') ){
    $config->addConfig('search', 'turn_offplugin_shoppro', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_classifiedspro') ){
    $config->addConfig('search', 'turn_offplugin_classifiedspro', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_pages') ){
    $config->addConfig('search', 'turn_offplugin_pages', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_groups') ){
    $config->addConfig('search', 'turn_offplugin_groups', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_blogs') ){
    $config->addConfig('search', 'turn_offplugin_blogs', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_event') ){
    $config->addConfig('search', 'turn_offplugin_event', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_fanpage') ){
    $config->addConfig('search', 'turn_offplugin_fanpage', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_html') ){
    $config->addConfig('search', 'turn_offplugin_html', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_games') ){
    $config->addConfig('search', 'turn_offplugin_games', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_adsense') ){
    $config->addConfig('search', 'turn_offplugin_adsense', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_mochigames') ){
    $config->addConfig('search', 'turn_offplugin_mochigames', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_basepages') ){
    $config->addConfig('search', 'turn_offplugin_basepages', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_adspro') ){
    $config->addConfig('search', 'turn_offplugin_adspro', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_map') ){
    $config->addConfig('search', 'turn_offplugin_map', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_wiki') ){
    $config->addConfig('search', 'turn_offplugin_wiki', "1", '');
}

