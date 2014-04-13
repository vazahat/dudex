<?php

Updater::getLanguageService()->importPrefixFromZip(dirname(__FILE__) . DS . 'langs.zip', 'search');

$config = OW::getConfig();
if ( !$config->configExists('search', 'bg_results_topsearchbar') ){
    $config->addConfig('search', 'bg_results_topsearchbar', "", '');
}

/*
$config = OW::getConfig();
if ( !$config->configExists('search', 'turn_offplugin_map') ){
    $config->addConfig('search', 'turn_offplugin_map', "0", '');
}
if ( !$config->configExists('search', 'turn_offplugin_wiki') ){
    $config->addConfig('search', 'turn_offplugin_wiki', "1", '');
}
if ( !$config->configExists('search', 'allow_ads_adsense') ){
    $config->addConfig('search', 'allow_ads_adsense', "1", '');
}
if ( !$config->configExists('search', 'allow_ads_adspro') ){
    $config->addConfig('search', 'allow_ads_adspro', "0", '');
}
if ( !$config->configExists('search', 'allow_ads_ads') ){
    $config->addConfig('search', 'allow_ads_ads', "1", '');
}
if ( !$config->configExists('search', 'turn_offplugin_news') ){
    $config->addConfig('search', 'turn_offplugin_news', "0", '');
}

*/
/*
BOL_ComponentAdminService::getInstance()->deleteWidget('SEARCH_CMP_IndexWidgetm');
BOL_ComponentAdminService::getInstance()->deleteWidget('SEARCH_CMP_IndexWidgetdm');
$cmpService = BOL_ComponentAdminService::getInstance();

$widget = $cmpService->addWidget('SEARCH_CMP_IndexWidgetm');
$placeWidget = $cmpService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_INDEX);
$cmpService->addWidgetToPosition($placeWidget, BOL_ComponentAdminService::SECTION_LEFT,1);

$widgetService = BOL_ComponentAdminService::getInstance();
$widget = $widgetService->addWidget('SEARCH_CMP_IndexWidgetdm');
$placeWidget = $widgetService->addWidgetToPlace($widget, BOL_ComponentAdminService::PLACE_DASHBOARD);
$widgetService->addWidgetToPosition($placeWidget, BOL_ComponentService::SECTION_RIGHT,1);
*/

