<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Featured FAQ widget
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.components
 * @since 1.0
 */
class OCSFAQ_CMP_FeaturedWidget extends BASE_CLASS_Widget
{
    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();

        $list = OCSFAQ_BOL_FaqService::getInstance()->getFeaturedQuestionList();
        
        if ( !$list )
        {
        	$this->setVisible(false);
        }
        
        $this->assign('list', $list);
        $this->assign('expand', OW::getConfig()->getValue('ocsfaq', 'expand_answers'));
    }

    public static function getStandardSettingValueList()
    {
        return array(
        	self::SETTING_WRAP_IN_BOX => true,
        	self::SETTING_SHOW_TITLE => true,
        	self::SETTING_ICON => self::ICON_HELP,
        	self::SETTING_TITLE => OW::getLanguage()->text('ocsfaq', 'widget_title')
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }
}