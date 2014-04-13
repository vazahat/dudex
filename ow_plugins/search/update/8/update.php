<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

$config = OW::getConfig();
if ( !$config->configExists('search', 'horizontal_position') ){
    $config->addConfig('search', 'horizontal_position', "0", 'position horizontal');
}
if ( !$config->configExists('search', 'vertical_position') ){
    $config->addConfig('search', 'vertical_position', "0", 'vertical_position');
}
if ( !$config->configExists('search', 'zindex_position') ){
    $config->addConfig('search', 'zindex_position', "101", 'zindex_position');
}
if ( !$config->configExists('search', 'width_topsearchbar') ){
    $config->addConfig('search', 'width_topsearchbar', "250", 'width_topsearchbar');
}
if ( !$config->configExists('search', 'turn_off_topsearchbar') ){
    $config->addConfig('search', 'turn_off_topsearchbar', "0", '');
}
