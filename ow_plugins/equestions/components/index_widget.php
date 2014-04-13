<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package equestions.components
 */
class EQUESTIONS_CMP_IndexWidget extends BASE_CLASS_Widget
{
    /**
     * @return Constructor.
     */
    public function __construct( BASE_CLASS_WidgetParameter $paramObj )
    {
        parent::__construct();

        $userId = OW::getUser()->getId();

        $cmp = new EQUESTIONS_CMP_MainFeed(time(), $userId, $paramObj->customParamList['count']);
        $cmp->setFeedType(EQUESTIONS_CMP_Feed::FEED_ALL);
        $cmp->setOrder($paramObj->customParamList['order']);

        $this->addComponent('feed', $cmp);

        if ( $paramObj->customParamList['addNew'] && EQUESTIONS_BOL_Service::getInstance()->isCurrentUserCanAsk() )
        {
            $add = new EQUESTIONS_CMP_QuestionAdd();
            $this->addComponent('add', $add);
        }
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_TITLE => OW::getLanguage()->text('equestions', 'widget_title'),
            self::SETTING_WRAP_IN_BOX => false,
            self::SETTING_ICON => self::ICON_LENS
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }

    public static function getSettingList()
    {
        $settingList['count'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => OW::getLanguage()->text('equestions', 'widget_settings_count'),
            'optionList' => array(5 => '5', '10' => 10, '20' => 20, '50' => 50, '100' => 100),
            'value' => 10
        );

        $settingList['order'] = array(
            'presentation' => self::PRESENTATION_SELECT,
            'label' => OW::getLanguage()->text('equestions', 'widget_settings_order'),
            'optionList' => array(EQUESTIONS_CMP_Feed::ORDER_LATEST => OW::getLanguage()->text('equestions', 'feed_order_latest'), EQUESTIONS_CMP_Feed::ORDER_POPULAR => OW::getLanguage()->text('equestions', 'feed_order_popular')),
            'value' => EQUESTIONS_CMP_Feed::ORDER_LATEST
        );

        $settingList['addNew'] = array(
            'presentation' => self::PRESENTATION_CHECKBOX,
            'label' => OW::getLanguage()->text('equestions', 'widget_settings_add_new'),
            'value' => true
        );

        return $settingList;
    }
}