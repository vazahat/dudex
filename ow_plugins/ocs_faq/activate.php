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
 * @package ow.ow_plugins.ocs_faq
 * @since 1.0
 */
OW::getNavigation()->addMenuItem(OW_Navigation::BOTTOM, 'ocsfaq.faq', 'ocsfaq', 'faq', OW_Navigation::VISIBLE_FOR_ALL);
OW::getNavigation()->addMenuItem(OW_Navigation::MOBILE_BOTTOM, 'ocsfaq.faq', 'ocsfaq', 'faq_mobile', OW_Navigation::VISIBLE_FOR_ALL);


$widget = BOL_ComponentAdminService::getInstance()->addWidget('OCSFAQ_CMP_FeaturedWidget');
$placeWidget = BOL_ComponentAdminService::getInstance()->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);
BOL_ComponentAdminService::getInstance()->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_RIGHT);