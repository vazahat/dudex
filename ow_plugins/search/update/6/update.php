<?php

if ( !Updater::getConfigService()->configExists('search', 'horizontal_position') )
{
    Updater::getConfigService()->addConfig('search', 'horizontal_position', 0);
}


Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');
