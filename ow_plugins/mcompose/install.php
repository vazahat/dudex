<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

try
{
    OW::getPluginManager()->addPluginSettingsRouteName('mcompose', 'mcompose-admin');
}
catch ( Exception $e )
{
    // Log
}

OW::getLanguage()->importPluginLangs(OW::getPluginManager()->getPlugin('mcompose')->getRootDir() . 'langs.zip', 'mcompose');

OW::getConfig()->addConfig('mcompose', "max_users", '10');
OW::getConfig()->addConfig("mcompose", "friends_enabled", 1);
OW::getConfig()->addConfig("mcompose", "groups_enabled", 1);
OW::getConfig()->addConfig("mcompose", "events_enabled", 1);