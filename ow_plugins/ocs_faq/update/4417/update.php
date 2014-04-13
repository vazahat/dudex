<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

$staticDir = OW_DIR_STATIC_PLUGIN . 'ocs_faq' . DS;
$staticImgDir = $staticDir  . 'img' . DS;

if ( !file_exists($staticDir) )
{
    @mkdir($staticDir);
    @chmod($staticDir, 0777);
}

if ( !file_exists($staticImgDir) )
{
    @mkdir($staticImgDir);
    @chmod($staticImgDir, 0777);
}

@copy(OW_DIR_PLUGIN . 'ocs_faq' . DS . 'static' . DS . 'img' . DS . 'oxwallcandystore-logo.jpg', $staticImgDir . 'oxwallcandystore-logo.jpg');

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'ocsfaq');