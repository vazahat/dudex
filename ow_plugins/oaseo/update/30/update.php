<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */
/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo
 * @since 1.0
 */

$dbo = Updater::getDbo();
$sqlErrors = array();

$queries = array( 
    "DROP TABLE  `".OW_DB_PREFIX."oaseo_data`"
);

foreach ( $queries as $query )
{
    try
    {
        $dbo->query($query);
    }
    catch( Exception $e )
    {
        $sqlErrors[] = $e;
    }
}

UPDATER::getConfigService()->saveConfig('oaseo', 'update_ts', 0);

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'oaseo');