<?php
//add menu main
OW::getNavigation()->addMenuItem(OW_Navigation::MAIN, 'yncontactimporter-import', 'yncontactimporter', 'friends_inviter', OW_Navigation::VISIBLE_FOR_MEMBER);
//add widget
$widget = BOL_ComponentAdminService::getInstance()->addWidget('YNCONTACTIMPORTER_CMP_Widget');
$placeWidget = BOL_ComponentAdminService::getInstance()->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_DASHBOARD);
BOL_ComponentAdminService::getInstance()->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_RIGHT, 0);
?>