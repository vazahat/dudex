<?php

/**
 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.

 * ---
 * Copyright (c) 2012, Sergey Kambalin
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
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package uavatars.classes
 */
class UAVATARS_CLASS_Plugin
{
    /**
     * Class instance
     *
     * @var UAVATARS_CLASS_Plugin
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UAVATARS_CLASS_Plugin
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     *
     * @var UAVATARS_CLASS_PhotoBridge
     */
    private $photoBridge;

    /**
     *
     * @var BOL_AvatarService
     */
    private $avatarService;

    /**
     *
     * @var UAVATARS_BOL_Service
     */
    private $uAvatarsService;

    private function __construct()
    {
        $this->photoBridge = UAVATARS_CLASS_PhotoBridge::getInstance();
        $this->avatarService = BOL_AvatarService::getInstance();
        $this->uAvatarsService = UAVATARS_BOL_Service::getInstance();
    }

    public function afterAvatarChange( OW_Event $event )
    {
        $params = $event->getParams();

        $userId = $params['userId'];
        $avatar = $this->avatarService->findByUserId($userId);

        if ( $params['upload'] )
        {
            $uAvatar = new UAVATARS_BOL_Avatar;

            $uAvatar->avatarId = $avatar->id;
            $uAvatar->userId = $userId;

            $avatarPath = $this->avatarService->getAvatarPath($userId, 3);
            $photoId = $this->photoBridge->addPhoto($userId, $avatarPath);

            if ( empty($photoId) )
            {
                return;
            }

            $uAvatar->photoId = $photoId;
            $uAvatar->timeStamp = time();
        }
        else
        {
            $uAvatar = $this->uAvatarsService->findLastByUserId($userId);
        }

        $avatarPreview = $this->avatarService->getAvatarPath($userId, 2);
        $fileName = $this->uAvatarsService->storeAvatarImage($avatarPreview);

        if ( empty($fileName) )
        {
            return;
        }

        if ( !empty($uAvatar->fileName) )
        {
            $userfilesDir = OW::getPluginManager()->getPlugin('uavatars')->getUserFilesDir();
            OW::getStorage()->removeFile($userfilesDir . $uAvatar->fileName);
        }

        $uAvatar->fileName = $fileName;

        $this->uAvatarsService->saveAvatar($uAvatar);
    }

    public function onCollectContent( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        if ( $params["placeName"] != BOL_ComponentService::PLACE_PROFILE || empty($params["entityId"]) )
        {
            return;
        }

        $userId = $params["entityId"];
        $avatar = UAVATARS_BOL_Service::getInstance()->findLastByUserId($userId);

        if ( $avatar === null )
        {
            return;
        }
        
        $staticUrl = OW::getPluginManager()->getPlugin('uavatars')->getStaticUrl();
        OW::getDocument()->addStyleSheet($staticUrl . 'style.css');
        OW::getDocument()->addScript($staticUrl . 'script.js');

        UAVATARS_CLASS_PhotoBridge::getInstance()->initPhotoFloatBox();

        $js = UTIL_JsGenerator::newInstance();

        $selector = '#avatar-console div:eq(0), .hg-avatar-image, .ow_profile_gallery_avatar_image';

        $js->addScript('$("' . $selector . '").addClass("ow_cursor_pointer");');

        $js->jQueryEvent($selector, 'click',
            'if ( $("' . $selector . '").is(event.target) ) UAVATARS.setPhoto(event.data.photoId);',
        array('event'), array(
            'photoId' => $avatar->photoId
        ));
        
        OW::getDocument()->addOnloadScript($js);
    }
    
    public function collectAdminNotifications( BASE_CLASS_EventCollector $e )
    {
        $language = OW::getLanguage();
        $e->add($language->text('uavatars', 'admin_plugin_required_notification', array(
            'pluginUrl' => 'http://www.oxwall.org/store/item/16',
            'settingUrl' => OW::getRouter()->urlForRoute('uavatars-admin')
        )));
    }
    
    public function initForNode( OW_Event $event )
    {
        $params = $event->getParams();
        $userId = $params["userId"];
        $node = $params["node"];
        
        $avatar = UAVATARS_BOL_Service::getInstance()->findLastByUserId($userId);

        if ( $avatar === null )
        {
            return;
        }
        
        $staticUrl = OW::getPluginManager()->getPlugin('uavatars')->getStaticUrl();
        OW::getDocument()->addStyleSheet($staticUrl . 'style.css');
        OW::getDocument()->addScript($staticUrl . 'script.js');

        UAVATARS_CLASS_PhotoBridge::getInstance()->initPhotoFloatBox();

        $js = UTIL_JsGenerator::newInstance();

        $js->addScript('$("' . $node . '").addClass("ow_cursor_pointer");');

        $js->jQueryEvent($node, 'click',
            'if ( $("' . $node . '").is(event.target) ) UAVATARS.setPhoto(event.data.photoId);',
        array('event'), array(
            'photoId' => $avatar->photoId
        ));
        
        OW::getDocument()->addOnloadScript($js);
    }

    public function init()
    {
        OW::getRouter()->addRoute(new OW_Route('uavatars-admin', 'admin/plugins/uavatars', 'UAVATARS_CTRL_Admin', 'index'));

        if ( !OW::getPluginManager()->isPluginActive('photo') )
        {
            OW::getEventManager()->bind('admin.add_admin_notification', array($this, 'collectAdminNotifications'));

            return;
        }
        
        OW::getEventManager()->bind('base.widget_panel.content.top', array($this, "onCollectContent"));
        OW::getEventManager()->bind('base.after_avatar_change', array($this, 'afterAvatarChange'));
        OW::getEventManager()->bind('uavatars.init_for_node', array($this, 'initForNode'));

        UAVATARS_CLASS_NewsfeedBridge::getInstance()->init();
    }
}
