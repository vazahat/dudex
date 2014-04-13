<?php

/**
 * Copyright (c) 2013, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package utags.classes
 */
class UTAGS_CLASS_NotificationsBridge
{
    /**
     * Class instance
     *
     * @var UTAGS_CLASS_NotificationsBridge
     */
    protected static $classInstance;

    const ACTION_TAG_ME = 'utags_tag_me';
    const ACTION_TAG_MY_PHOTO = 'utags_tag_my_photo';
    const TYPE_TAG_MY_PHOTO = 'utags_tag_my_photo';
    const TYPE_TAG_ME = 'utags_tag_me';

    /**
     * Returns class instance
     *
     * @return UTAGS_CLASS_NotificationsBridge
     */
    public static function getInstance()
    {
        if ( !isset(self::$classInstance) )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    public function __construct()
    {
        
    }
    
    public function isActive()
    {
        return OW::getPluginManager()->isPluginActive('notifications');
    }

    public function onItemRender( OW_Event $event )
    {
        $params = $event->getParams();

        if ( !in_array($params['entityType'], array(self::TYPE_TAG_ME, self::TYPE_TAG_MY_PHOTO)) )
        {
            return;
        }
        
        $tagId = intval($params['entityId']);
        $tag = UTAGS_BOL_Service::getInstance()->findTagById($tagId);
        $photoId = empty($tag->copyPhotoId) ? $tag->photoId : $tag->copyPhotoId;
        
        if ( $tag === null )
        {
            return;
        }
        
        UTAGS_CLASS_PhotoBridge::getInstance()->initPhotoFloatBox();

        $data = $event->getData();
        $data['url'] = 'javascript://';
        $data["contentImage"] = UTAGS_CLASS_PhotoBridge::getInstance()->getPreviewSrc($photoId);

        $uniqId = $params['key'];

        $js = UTIL_JsGenerator::newInstance();
        $js->addScript('UTAGS_Require();');
        
        $js->addScript('var busy = false; $("#' . $uniqId . '").click(function( e ) {
            if ( !$(e.target).is("a") ) {
                if (busy) return false;
                busy = true;
                UTAGS_Require(function( app ) {
                    busy = false;
                    app.PhotoLauncher.setPhoto({$photoId});
                    
                    app._afterPhotoActivate = function( photo ) {
                        if ( photo.fetched )
                            photo.showTags({$tagIds});
                        else
                            photo.afterFetch = function() {
                                photo.showTags({$tagIds});
                                photo.afterFetch = null;
                            };

                        app._afterPhotoActivate = null;
                    };
                });
            }
        });;', array(
            "photoId" => $tag->photoId,
            "tagIds" => array((int) $tag->id)
        ));

        OW::getDocument()->addOnloadScript($js->generateJs());

        $event->setData($data);
    }

    public function onTagRemove( OW_Event $event )
    {
        $params = $event->getParams();
        $tagId = $params['tagId'];

        $event = new OW_Event('notifications.remove', array(
            'entityType' => self::TYPE_TAG_MY_PHOTO,
            'entityId' => $tagId
        ));
        OW::getEventManager()->trigger($event);
        
        $event = new OW_Event('notifications.remove', array(
            'entityType' => self::TYPE_TAG_ME,
            'entityId' => $tagId
        ));
        OW::getEventManager()->trigger($event);
    }
    
    private function addNotification( $type, $action, UTAGS_BOL_Tag $tag, $userId, $avatar, $string, $content = null )
    {
        $photoId = $tag->photoId;

        $notificationParams = array(
            'pluginKey' => "utags",
            'action' => $action,
            'entityType' => $type,
            'entityId' => $tag->id,
            'userId' => $userId,
            'time' => time()
        );
        
        $notificationData = array(
            'string' => $string,
            'avatar' => $avatar,
            'content' => $content,
            'contentImage' => UTAGS_CLASS_PhotoBridge::getInstance()->getPreviewSrc($photoId),
            'url' => UTAGS_CLASS_PhotoBridge::getInstance()->getPhotoUrl($photoId)
        );

        $event = new OW_Event('notifications.add', $notificationParams, $notificationData);
        OW::getEventManager()->trigger($event);
    }
    
    public function onTagAdd( OW_Event $event )
    {
        $params = $event->getParams();
        /*@var $tag UTAGS_BOL_Tag */
        $tag = $params['tag'];
        $tagData = $tag->getData();
        
        $userId = $tag->userId;
        $photoOwnerId = UTAGS_CLASS_PhotoBridge::getInstance()->getPhotoOwnerId($tag->photoId);
        
        $photoOwnerMode = $userId == $photoOwnerId;
        $userTag = $tag->entityType == UTAGS_BOL_Service::ENTITY_TYPE_USER;
        
        if ( $photoOwnerMode && ( ($userTag && $userId == $tag->entityId) || !$userTag ) )
        {
            return;
        }
        
        $userIds = array( $userId, $photoOwnerId );
        if ( $userTag )
        {
            $userIds[] = $tag->entityId;
        }
        
        $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars($userIds, true, true, true, false);
        
        $assignVars = array(
            'user' => '<a href="' . $avatars[$userId]['url'] . '">' . $avatars[$userId]['title'] . '</a>'
        );
        
        if ( $userTag )
        {
            $assignVars["taggedUser"] = '<a href="' . $avatars[$tag->entityId]['url'] . '">' . $avatars[$tag->entityId]['title'] . '</a>';
            $assignVars["photoOwner"] = '<a href="' . $avatars[$photoOwnerId]['url'] . '">' . $avatars[$photoOwnerId]['title'] . '</a>';
            
            if ( $photoOwnerMode )
            {
                $this->addNotification(self::TYPE_TAG_ME, self::ACTION_TAG_ME, $tag, $tag->entityId, $avatars[$userId], array(
                    'key' => 'utags+notifications_owner_tagged_you',
                    'vars' => $assignVars
                ));
            }
            else 
            {
                if ( $photoOwnerId == $tag->entityId )
                {
                    $langKey = 'utags+notifications_tagged_you_on_your_photo';
                }
                else if ( $tag->entityId == $tag->userId ) 
                {
                    $langKey = 'utags+notifications_tagged_himself_on_your_photo';
                }
                else
                {
                    $langKey = 'utags+notifications_tagged_user_on_your_photo';
                }
                
                $this->addNotification(self::TYPE_TAG_MY_PHOTO, self::ACTION_TAG_MY_PHOTO, $tag, $photoOwnerId, $avatars[$userId], array(
                    'key' => $langKey,
                    'vars' => $assignVars
                ));
                
                if ( $photoOwnerId != $tag->entityId && $tag->userId != $tag->entityId )
                {
                    $this->addNotification(self::TYPE_TAG_ME, self::ACTION_TAG_ME, $tag, $tag->entityId, $avatars[$userId], array(
                        'key' => 'utags+notifications_user_tagged_you',
                        'vars' => $assignVars
                    ));
                }
            }
        }
        else if ( !$photoOwnerMode )
        {
            $tagUrl = OW::getRouter()->urlForRoute("view_tagged_photo_list", array(
                "tag" => $tagData["data"]["text"]
            ));
            
            $assignVars["tag"] = '<a href="' . $tagUrl . '">' . $tagData["data"]["text"] . '</a>';
            
            $this->addNotification(self::TYPE_TAG_MY_PHOTO, self::ACTION_TAG_MY_PHOTO, $tag, $photoOwnerId, $avatars[$userId], array(
                'key' => 'utags+notifications_tagged_your_photo',
                'vars' => $assignVars
            ), $tagData["data"]["text"]);
        }
    }
    
    public function onCollectActions( BASE_CLASS_EventCollector $e )
    {
        $e->add(array(
            'section' => "utags",
            'action' => self::ACTION_TAG_ME,
            'sectionIcon' => 'ow_ic_picture',
            'sectionLabel' => OW::getLanguage()->text("utags", 'notifications_section_label'),
            'description' => OW::getLanguage()->text("utags", 'notifications_setting_tag_me'),
            'selected' => true
        ));

        $e->add(array(
            'section' => "utags",
            'action' => self::ACTION_TAG_MY_PHOTO,
            'sectionIcon' => 'ow_ic_picture',
            'sectionLabel' => OW::getLanguage()->text("utags", 'notifications_section_label'),
            'description' => OW::getLanguage()->text("utags", 'notifications_setting_tag_my_photo'),
            'selected' => true
        ));
    }

    public function genericInit()
    {
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_BEFORE_DELETE, array($this, 'onTagRemove'));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_AFTER_ADD, array($this, 'onTagAdd'));
    }
    
    public function init()
    {
        if ( !$this->isActive() ) return;
        
        $this->genericInit();
        
        OW::getEventManager()->bind('notifications.collect_actions', array($this, 'onCollectActions'));
        OW::getEventManager()->bind('notifications.on_item_render', array($this, 'onItemRender'));
    }
}