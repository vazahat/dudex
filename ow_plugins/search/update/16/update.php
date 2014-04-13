<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

$config = OW::getConfig();
if ( !$config->configExists('search', 'search_position') ){
    $config->addConfig('search', 'search_position', "absolute", '');
}
if ( !$config->configExists('search', 'height_topsearchbar') ){
    $config->addConfig('search', 'height_topsearchbar', "25", '');
}

