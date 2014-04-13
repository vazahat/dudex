<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * /activate.php
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_sitestats
 * @since 1.5
 */
$cmpService = BOL_ComponentAdminService::getInstance();

$widget = $cmpService->addWidget('OCSSITESTATS_CMP_IndexWidget');
$placeWidget = $cmpService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);

$activeThemeName = OW::getConfig()->getValue('base', 'selectedTheme');
$theme = BOL_ThemeService::getInstance()->getThemeObjectByName($activeThemeName)->getDto();
$sidebarPos = $theme->getSidebarPosition();

if ( in_array($sidebarPos, array('left', 'right')) )
{
    $cmpService->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_SIDEBAR);
}