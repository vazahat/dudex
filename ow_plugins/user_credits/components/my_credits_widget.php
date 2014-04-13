<?php

/**
 * Copyright (c) 2009, Skalfa LLC
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * My credits widget component
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.ow_plugins.user_credits.components
 * @todo Remove widget
 * @since 1.0
 */
class USERCREDITS_CMP_MyCreditsWidget extends BASE_CLASS_Widget
{
    /**
     * @var USERCREDITS_BOL_CreditsService
     */
    private $creditsService;

    /**
     * Class constructor
     */
    public function __construct( BASE_CLASS_WidgetParameter $paramObj )
    {
        parent::__construct();

        $this->creditsService = USERCREDITS_BOL_CreditsService::getInstance();
        
        $userId = OW::getUser()->getId();
        
        if ( !$userId )
        {
            $this->setVisible(false); 
            return;
        }
        
        $balance = $this->creditsService->getCreditsBalance($userId);
                
        $this->assign('balance', $balance);
                
        $this->setSettingValue(
            self::SETTING_TOOLBAR,
            array(
                array(
                    'label' => OW::getLanguage()->text('usercredits', 'get_credits'),
                    'href' => OW::getRouter()->urlForRoute('usercredits.buy_credits')
                )
            )
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_MEMBER;
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('usercredits', 'my_credits'),
            self::SETTING_ICON => self::ICON_INFO,
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true
        );
    }
}