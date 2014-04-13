<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

$config = OW::getConfig();
if ( !$config->configExists('search', 'hmanyitems_show_topsearchbarlist') ){
    $config->addConfig('search', 'hmanyitems_show_topsearchbarlist', "3", '');
}

if ( !$config->configExists('search', 'maxallitems_topsearchbarlist') ){
    $config->addConfig('search', 'maxallitems_topsearchbarlist', "8", '');
}
