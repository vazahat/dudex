<?php

if ( !Updater::getConfigService()->configExists('equestions', 'allow_popups') )
{
    Updater::getConfigService()->addConfig('equestions', 'allow_popups', 1);
}

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'equestions');
