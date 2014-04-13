<?php

/**
 * Copyright (c) 2014, Kairat Bakytow
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/** 
 * 
 *
 * @author Kairat Bakytow <kainisoft@gmail.com>
 * @package ow_plugins.profileprogressbar.components
 * @since 1.0
 */
class PROFILEPROGRESSBAR_CMP_Widget extends BASE_CLASS_Widget
{
    public function __construct( BASE_CLASS_WidgetParameter $paramObj )
    {
        parent::__construct();
        
        $theme = OW::getConfig()->getValue('profileprogressbar', 'theme');
        OW::getDocument()->addStyleSheet(OW::getPluginManager()->getPlugin('profileprogressbar')->getStaticCssUrl() . $theme . '.css');
        
        $data = PROFILEPROGRESSBAR_BOL_Service::getInstance()->getProgressbarData($paramObj->additionalParamList['entityId'], (bool)OW::getConfig()->getValue('profileprogressbar', 'show_hint') && $paramObj->additionalParamList['entityId'] == OW::getUser()->getId());
        
        if ( empty($data) )
        {
            $this->setVisible(FALSE);
        }
        
        $percent = round(($data[PROFILEPROGRESSBAR_BOL_Service::KEY_PROGRESSBAR][PROFILEPROGRESSBAR_BOL_Service::COUNT_COMPLETED_QUESTION] * 100) / $data[PROFILEPROGRESSBAR_BOL_Service::KEY_PROGRESSBAR][PROFILEPROGRESSBAR_BOL_Service::COUNT_QUESTION]);
        $this->assign('percent', $percent > 100 ? 100 : $percent);
        
        if ( !empty($data[PROFILEPROGRESSBAR_BOL_Service::KEY_HINT]) )
        {
            $document = OW::getDocument();
            $plugin = OW::getPluginManager()->getPlugin('profileprogressbar');
            
            $document->addStyleSheet($plugin->getStaticCssUrl() . 'tipTip.css');
            $document->addScript($plugin->getStaticJsUrl() . 'jquery.tipTip.minified.js');
            
            $document->addOnloadScript(
                UTIL_JsGenerator::composeJsString(
                    ';$("#profile_progressbar").tipTip({
                        maxWidth: "auto",
                        content: {$hint}
                    });', array('hint' => $data[PROFILEPROGRESSBAR_BOL_Service::KEY_HINT])
                )
            );
        }
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('profileprogressbar', 'widget_caption'),
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true,
            self::SETTING_ICON => self::ICON_USER
        );
    }
    
    public static function getAccess()
    {
        return self::ACCESS_MEMBER;
    }
}
