<?php


/***
 * This software is intended for use with Oxwall Free Community Software
 * http://www.oxwall.org/ and is a proprietary licensed product.
 * For more information see License.txt in the plugin folder.

 * =============================================================================
 * Copyright (c) 2012 by Aron. All rights reserved.
 * =============================================================================


 * Redistribution and use in source and binary forms, with or without modification, are not permitted provided.
 * Pass on to others in any form are not permitted provided.
 * Sale are not permitted provided.
 * Sale this product are not permitted provided.
 * Gift this product are not permitted provided.
 * This plugin should be bought from the developer by paying money to PayPal account: biuro@grafnet.pl
 * Legal purchase is possible only on the web page URL: http://www.oxwall.org/store
 * Modyfing of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 * Modifying source code, all information like:copyright must remain.
 * Official website only: http://test.a6.pl
 * Full license available at: http://test.a6.pl


 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
***/



/*
$sql = "DELETE FROM `".OW_DB_PREFIX."base_component_place` WHERE `uniqName` = 'index-OCSSLIDER_CMP_SliderWidget' AND `id` = 100413";
OW::getDbo()->query($sql);

$widget = BOL_ComponentAdminService::getInstance()->addWidget('OCSSLIDER_CMP_SliderWidget');
$placeWidget = BOL_ComponentAdminService::getInstance()->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);
BOL_ComponentAdminService::getInstance()->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_TOP);
*/

/*
$cmpService = BOL_ComponentAdminService::getInstance();

//$widget = $cmpService->addWidget('PAGES_CMP_IndexWidget');
$widget = $cmpService->addWidget('WIDGETPLUS_CMP_Contactimporter');
$placeWidget = $cmpService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);
$cmpService->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_LEFT,0);
*/

$cmpService = BOL_ComponentAdminService::getInstance();

$widget = $cmpService->addWidget('SEARCH_CMP_IndexWidget');
$placeWidget = $cmpService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);
$cmpService->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_LEFT,0);

$widget = $cmpService->addWidget('SEARCH_CMP_IndexWidgetm');
$placeWidget = $cmpService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);
$cmpService->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_LEFT,1);


$widgetService = BOL_ComponentAdminService::getInstance();
$widget = $widgetService->addWidget('SEARCH_CMP_IndexWidgetd');
$placeWidget = $widgetService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_DASHBOARD);
$widgetService->addWidgetToPosition($placeWidget, BOL_ComponentService::SECTION_RIGHT,0);

$widgetService = BOL_ComponentAdminService::getInstance();
$widget = $widgetService->addWidget('SEARCH_CMP_IndexWidgetdm');
$placeWidget = $widgetService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_DASHBOARD);
$widgetService->addWidgetToPosition($placeWidget, BOL_ComponentService::SECTION_RIGHT,1);

OW::getNavigation()->addMenuItem(OW_Navigation::MAIN, 'search.start', 'search', 'main_menu_item', OW_Navigation::VISIBLE_FOR_ALL);


