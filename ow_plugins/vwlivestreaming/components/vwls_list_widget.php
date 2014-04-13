<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2011, Oxwall Foundation
 * All rights reserved.

 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the
 * following conditions are met:
 *
 *  - Redistributions of source code must retain the above copyright notice, this list of conditions and
 *  the following disclaimer.
 *
 *  - Redistributions in binary form must reproduce the above copyright notice, this list of conditions and
 *  the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 *  - Neither the name of the Oxwall Foundation nor the names of its contributors may be used to endorse or promote products
 *  derived from this software without specific prior written permission.

 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES,
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED
 * AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/**
 * Vwls list widget
 *
 * @author Egor Bulgakov <egor.bulgakov@gmail.com>
 * @package ow.plugin.vwls.components
 * @since 1.0
 */
class VWLS_CMP_VwlsListWidget extends BASE_CLASS_Widget
{

    /**
     * @return Constructor.
     */
    public function __construct( BASE_CLASS_WidgetParameter $paramObj )
    {
        parent::__construct();

        $clipService = VWLS_BOL_ClipService::getInstance();

        $count = isset($paramObj->customParamList['clipCount']) ? (int) $paramObj->customParamList['clipCount'] : 4;

        $lang = OW::getLanguage();

        $this->assign('showTitles', $paramObj->customParamList['showTitles']);

/**        $latest = $clipService->findClipsList('latest', 1, $count);
        if ( $latest )
        {
            $latest[0]['code'] = $this->prepareClipCode($latest[0]['code'], $latest[0]['provider']);
        }
        $this->assign('latest', $latest);
*/

        $online = $clipService->findClipsList('online', 1, $count);
        if ( $online )
        {
            $online[0]['code'] = $this->prepareClipCode($online[0]['code'], $latest[0]['provider']);
        }
        
        $this->assign('online', $online);

        $userId = OW::getUser()->getId();
        $this->assign('userId', $userId);

        if ( !$latest && !OW::getUser()->isAuthorized('vwls', 'add') )
        {
            $this->setVisible(false);

            return;
        }
        
/**        $featured = $clipService->findClipsList('featured', 1, $count);
        if ( $featured )
        {
            $featured[0]['code'] = $this->prepareClipCode($featured[0]['code'], $featured[0]['provider']);
        }
        $this->assign('featured', $featured);
*/
        $toprated = $clipService->findClipsList('toprated', 1, $count);
        if ( $toprated )
        {
            $toprated[0]['code'] = $this->prepareClipCode($toprated[0]['code'], $toprated[0]['provider']);
        }
        $this->assign('toprated', $toprated);

        $menuItems['latest'] = array(
            'label' => $lang->text('vwls', 'menu_latest'),
            'id' => 'vwls-widget-menu-latest',
            'contId' => 'vwls-widget-latest',
            'active' => true
        );
        
/**        if ( $featured )
        {
            $menuItems['featured'] = array(
                'label' => $lang->text('vwls', 'menu_featured'),
                'id' => 'vwls-widget-menu-featured',
                'contId' => 'vwls-widget-featured',
            );
        }
*/        
        $menuItems['toprated'] = array(
            'label' => $lang->text('vwls', 'menu_toprated'),
            'id' => 'vwls-widget-menu-toprated',
            'contId' => 'vwls-widget-toprated',
        );

        if ( !$paramObj->customizeMode )
        {
            $this->addComponent('menu', new BASE_CMP_WidgetMenu($menuItems));
        }

        $this->assign('items', $menuItems);
        
        $toolbars = self::getToolbar();
        $this->assign('toolbars', $toolbars);

        if ( $latest )
        {
            $this->setSettingValue(self::SETTING_TOOLBAR, $toolbars['latest']);
        }
    }

    public static function getSettingList()
    {
        $lang = OW::getLanguage();

        $settingList = array();

        $settingList['clipCount'] = array(
            'presentation' => self::PRESENTATION_NUMBER,
            'label' => $lang->text('vwls', 'cmp_widget_vwls_count'),
            'value' => 3
        );

        $settingList['showTitles'] = array(
            'presentation' => self::PRESENTATION_CHECKBOX,
            'label' => $lang->text('vwls', 'cmp_widget_user_vwls_show_titles'),
            'value' => true
        );

        return $settingList;
    }

    public static function validateSettingList( $settingList )
    {
        $validationMessage = OW::getLanguage()->text('vwls', 'cmp_widget_vwls_count_msg');

        if ( !preg_match('/^\d+$/', $settingList['clipCount']) )
        {
            throw new WidgetSettingValidateException($validationMessage, 'clipCount');
        }
        if ( $settingList['clipCount'] > 20 )
        {
            throw new WidgetSettingValidateException($validationMessage, 'clipCount');
        }
    }

    public static function getAccess()
    {
        return self::ACCESS_ALL;
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('vwls', 'vwls_list_widget'),
            self::SETTING_ICON => self::ICON_VIDEO,
            self::SETTING_SHOW_TITLE => true
        );
    }

    private function prepareClipCode( $code, $provider )
    {
/**        $clipService = VWLS_BOL_ClipService::getInstance();

        $code = $clipService->validateClipCode($code, $provider);
        $code = $clipService->addCodeParam($code, 'wmode', 'transparent');
        
        $config = OW::getConfig();
        $playerWidth = $config->getValue('vwls', 'player_width');
        $playerHeight = $config->getValue('vwls', 'player_height');

        $code = $clipService->formatClipDimensions($code, $playerWidth, $playerHeight);
*/
        return $code;
    }

    private static function getToolbar()
    {
        $lang = OW::getLanguage();

        $items = array('latest', 'featured', 'toprated');

        $auth = OW::getUser()->isAuthorized('vwls', 'add');

        foreach ( $items as $tbItem )
        {
            if ( $auth )
            {
                $toolbars[$tbItem][] = array(
                    'href' => OW::getRouter()->urlFor('VWLS_CTRL_Add'),
                    'label' => $lang->text('vwls', 'add_new')
                );
            }

            $toolbars[$tbItem][] = array(
                'href' => OW::getRouter()->urlForRoute('vwview_list_ls', array('listType' => $tbItem)),
                'label' => $lang->text('base', 'view_all')
            );
        }

        return $toolbars;
    }
}
