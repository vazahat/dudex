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
 * @author Aybat Duyshokov <duyshokov@gmail.com>
 * @package ow_system_plugins.base.components
 * @since 1.0

class VWVC_CMP_FriendVwvcOnlineWidget extends BASE_CMP_UsersWidget
{
    public function getData( BASE_CLASS_WidgetParameter $params )
    {
        if( !OW::getUser()->isAuthenticated() || !OW::getEventManager()->call('plugin.friends') )
        {
            $this->setVisible(false);
            return;
        }
        
//        $count = (int)$params->customParamList['count'];
        $count = 4;
        
        $language = OW::getLanguage();
        $service = VWVC_BOL_ClipService::getInstance();

        $friendsIdList = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => OW::getUser()->getId()));
        $clips = $service->findClipsList('online', 1, $count);
        
 *//**        if ( (!$params->customizeMode && empty($users) ) )
        {
            $this->setVisible(false);
        }        
        
        $online = VWVC_BOL_ClipDao::getInstance();

        $a= array ();
        $a[0]=1;
        $a[1]=2;
        return array(
            'online_vwvc' => array(
                'menu-label' => $language->text('vwvc', 'user_list_menu_item_vwvc'),
                'userIds' => $a,
//                'userIds' => $this->getIdList($users),
                'toolbar' => false, //TODO complete
                'menu_active' => true
            )
        );
    }

    public static function getSettingList()
    {
        $settingList = array();
        $settingList['count'] = array(
            'presentation' => 'number',
            'label' => 'Count',
            'value' => '9'
        );

        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('vwvc', 'dashboard_widget_title'),
            self::SETTING_ICON => self::ICON_USER,
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_MEMBER;
    }
}
*/

class VWVC_CMP_FriendVwvcOnlineWidget extends BASE_CLASS_Widget
{

    /**
     * @var VWVC_BOL_ClipService 
     */
    private $clipService;

    public function __construct( BASE_CLASS_WidgetParameter $params )
    {
        parent::__construct();

        if ( empty($params->additionalParamList['entityId']) )
        {
            $userId = OW::getUser()->getId();
        }
        else
            $userId = $params->additionalParamList['entityId'];

        $friendsIdList = OW::getEventManager()->call('plugin.friends.get_friend_list', array('userId' => OW::getUser()->getId()));

        $count = 25;  
        $this->clipService = VWVC_BOL_ClipService::getInstance();
        $clips = $this->clipService->findClipsList('online', '1', $count);

        $friendsCount = count ($friendsIdList);
        $result = array ();
        $resultx = "";
        foreach ($friendsIdList as $friendId) {
          $resultx = $this->clipService->findClipsByFriendId($friendId, $clips);
          if ($resultx != "") array_push ($result, $resultx);
        }
        $html = "";
        if ($result != "") {
          foreach ($result as $part) {
            $partx = explode (":", $part);
            $displayName = BOL_UserService::getInstance()->getDisplayName($partx[0]);
            $html .= $displayName."&nbsp;";
  
            $active_in = OW::getLanguage()->text('vwvc', 'active_in');
            $html .= $active_in;
  
            $roomIdx = explode ("|", $partx[1]);
            foreach ($roomIdx as $roomId) {
              $clipx = $this->clipService->findClipById ($roomId);
              $urlRoom = OW::getRouter()->urlForRoute('vwview_clip', array('id' => $roomId));
              $html .= '&nbsp;<a href ="'.$urlRoom.'">'.$clipx->title.'</a>';
            }
          $html .= "<br />";
          }
        $this->assign('content', $html);
//        $this->assign('content', var_dump ($resultx));
        } else $this->assign('content', OW::getLanguage()->text('vwvc', 'no_active_room'));

    }

    public static function getSettingList()
    {
        $settingList = array();

        return $settingList;
    }

    public static function getStandardSettingValueList()
    {
        return array(
            self::SETTING_TITLE => OW::getLanguage()->text('vwvc', 'widget_title_dashboard'),
            self::SETTING_ICON => 'ow_ic_user',
            self::SETTING_SHOW_TITLE => true,
            self::SETTING_WRAP_IN_BOX => true
        );
    }

    public static function getAccess()
    {
        return self::ACCESS_MEMBER;
    }
}
