<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Site stats index widget
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_sitestats.components
 * @since 1.5
 */
class OCSSITESTATS_CMP_IndexWidget extends BASE_CLASS_Widget
{
    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();
        
        $lang = OW::getLanguage();
        $service = OCSSITESTATS_BOL_Service::getInstance();
        
        $this->assign('data', $service->getStatistics());
        $this->assign('zeroValues', OW::getConfig()->getValue('ocssitestats', 'zero_values'));
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('ocssitestats', 'index_widget_title'),
            self::SETTING_ICON => 'ow_ic_info',
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }
}