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
class UTAGS_CLASS_NewsfeedBridge
{
    const TYPE_TEXT_TAG = 'utags_text_tag';
    const TYPE_USER_TAG = 'utags_user_tag';
    
    /**
     * Class instance
     *
     * @var UTAGS_CLASS_NewsfeedBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UTAGS_CLASS_NewsfeedBridge
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
     * @var OW_Plugin
     */
    private $plugin;

    public function __construct()
    {
        $this->plugin = OW::getPluginManager()->getPlugin('utags');
    }

    public function isActive()
    {
        return OW::getPluginManager()->isPluginActive("newsfeed");
    }

    public function onItemRender( OW_Event $event )
    {
        $params = $event->getParams();
        $data = $event->getData();

        if ( empty($data["tagId"]) ) 
        {
            return;
        }
        
        $script = '$(".ow_newsfeed_item_picture a", "#' . $params['autoId'] . '").on("click", function(e) { UTAGS_Require(function( app ) {
            app._afterPhotoActivate = function( photo ) {
                if ( photo.fetched )
                    photo.showTags(' . json_encode(array( (int) $data["tagId"])) . ');
                else
                    photo.afterFetch = function() {
                        photo.showTags(' . json_encode(array( (int) $data["tagId"])) . ');
                        photo.afterFetch = null;
                    };
                    
                app._afterPhotoActivate = null;
            };
        });});';

        OW::getDocument()->addOnloadScript($script);
    }

    private function addActivity( $type, $visibility, UTAGS_BOL_Tag $tag, $userId, $string, $feed = null, $postOnAuthorFeed = true )
    {
        $activityParams = array(
            'activityType' => $type,
            'activityId' => $tag->id,
            'entityId' => $tag->photoId,
            'entityType' => UTAGS_CLASS_PhotoBridge::FEED_ENTITY_TYPE,
            'userId' => $userId,
            'pluginKey' => "photo",
            "postOnUserFeed" => $postOnAuthorFeed,
            "visibility" => $visibility
        );
        
        if ( !empty($feed) )
        {
            $activityParams["feedType"] = $feed["feedType"];
            $activityParams["feedId"] = $feed["feedId"];
        }
        
        $activityData = array(
            'string' => $string,
            'line' => null,
            "tagId" => $tag->id
        );

        $event = new OW_Event('feed.activity', $activityParams, $activityData);
        
        OW::getEventManager()->trigger($event);
    }
    
    public function onTagAdd( OW_Event $event )
    {
        $params = $event->getParams();
        /*@var $tag UTAGS_BOL_Tag */
        $tag = $params['tag'];
        $tagData = $tag->getData();
        $language = OW::getLanguage();
        
        $userId = $tag->userId;
        $photoOwnerId = UTAGS_CLASS_PhotoBridge::getInstance()->getPhotoOwnerId($tag->photoId);
        
        $photoOwnerMode = $userId == $photoOwnerId;
        $userTag = $tag->entityType == UTAGS_BOL_Service::ENTITY_TYPE_USER;
        
        $userIds = array( $userId, $photoOwnerId );
        if ( $userTag )
        {
            $userIds[] = $tag->entityId;
        }
        
        $avatars = BOL_AvatarService::getInstance()->getDataForUserAvatars($userIds, true, true, true, false);
        
        $assignVars = array(
            'user' => '<a href="' . $avatars[$userId]['url'] . '">' . $avatars[$userId]['title'] . '</a>'
        );
        
        $assignVars["photoOwner"] = '<a href="' . $avatars[$photoOwnerId]['url'] . '">' . $avatars[$photoOwnerId]['title'] . '</a>';
        
        if ( $userTag )
        {
            $assignVars["taggedUser"] = '<a href="' . $avatars[$tag->entityId]['url'] . '">' . $avatars[$tag->entityId]['title'] . '</a>';
            
            $feed = array(
                "feedType" => "user",
                "feedId" => $tag->entityId
            );
            
            if ( $photoOwnerId == $tag->userId )
            {
                $langKey = 'utags+newsfeed_owner_user_tag';
                
                if ( $tag->entityId == $tag->userId ) 
                {
                    $langKey = 'utags+newsfeed_owner_themselves_tag';
                    $feed = null;
                }
            }
            else if ( $photoOwnerId == $tag->entityId )
            {
                $langKey = 'utags+newsfeed_user_photo_user_tagged';
            }
            else 
            {
                $langKey = 'utags+newsfeed_user_tagged';
            }
            
            $this->addActivity(
                    self::TYPE_USER_TAG,
                    NEWSFEED_BOL_Service::VISIBILITY_FULL,
                    $tag,
                    $tag->userId,
                    array("key" => $langKey, "vars" => $assignVars), 
                    $feed);
        }
        else
        {
            $tagUrl = OW::getRouter()->urlForRoute("view_tagged_photo_list", array(
                "tag" => $tagData["data"]["text"]
            ));
            
            $assignVars["tag"] = '<a href="' . $tagUrl . '">' . $tagData["data"]["text"] . '</a>';
            
            $feed = null;
            
            if ( $photoOwnerId == $tag->userId )
            {
                $langKey = 'utags+newsfeed_owner_text_tag';
            }
            else 
            {
                $langKey = 'utags+newsfeed_text_tagged';
                $feed = array(
                    "feedType" => "user",
                    "feedId" => $photoOwnerId
                );
            }
            
            $this->addActivity(
                    self::TYPE_TEXT_TAG,
                    NEWSFEED_BOL_Service::VISIBILITY_FULL,
                    $tag,
                    $tag->userId,
                    array("key" => $langKey, "vars" => $assignVars),
                    $feed);
        }
    }
    
    public function onTagRemove( OW_Event $event )
    {
        $params = $event->getParams();
        $tagId = $params['tagId'];
        $tag = UTAGS_BOL_Service::getInstance()->findTagById($tagId);
        
        if ( $tag === null )
        {
            return;
        }
        
        $activityParams = array(
            'entityType' => UTAGS_CLASS_PhotoBridge::FEED_ENTITY_TYPE,
            'entityId' => $tag->photoId,
            'activityId' => $tag->id
        );
        
        if ( $tag->entityType == UTAGS_BOL_Service::ENTITY_TYPE_CUSTOM )
        {
            $activityParams["activityType"] = self::TYPE_TEXT_TAG;
        }
        
        if ( $tag->entityType == UTAGS_BOL_Service::ENTITY_TYPE_USER )
        {
            $activityParams["activityType"] = self::TYPE_USER_TAG;
        }
        
        OW::getEventManager()->trigger(new OW_Event('feed.delete_activity', $activityParams));
    }

    public function onCollectConfigurableActivity( BASE_CLASS_EventCollector $event )
    {
        $language = OW::getLanguage();
        $event->add(array(
            'label' => $language->text('utags', 'feed_content_label'),
            'activity' => array(
                UTAGS_CLASS_NewsfeedBridge::TYPE_USER_TAG . ':' . UTAGS_CLASS_PhotoBridge::FEED_ENTITY_TYPE,
                UTAGS_CLASS_NewsfeedBridge::TYPE_TEXT_TAG . ':' . UTAGS_CLASS_PhotoBridge::FEED_ENTITY_TYPE
            )
        ));
    }

    public function onCollectPrivacy( BASE_CLASS_EventCollector $event )
    {
        $event->add(array(UTAGS_CLASS_NewsfeedBridge::TYPE_USER_TAG . ':' . UTAGS_CLASS_PhotoBridge::FEED_ENTITY_TYPE, UTAGS_CLASS_PrivacyBridge::ACTION_VIEW_TAGS));
        $event->add(array(UTAGS_CLASS_NewsfeedBridge::TYPE_TEXT_TAG . ':' . UTAGS_CLASS_PhotoBridge::FEED_ENTITY_TYPE, UTAGS_CLASS_PrivacyBridge::ACTION_VIEW_TAGS));
    }

    public function genericInit()
    {
        OW::getEventManager()->bind('feed.collect_configurable_activity', array($this, 'onCollectConfigurableActivity'));
        OW::getEventManager()->bind('feed.collect_privacy', array($this, 'onCollectPrivacy'));
        
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_BEFORE_DELETE, array($this, 'onTagRemove'));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_AFTER_ADD, array($this, 'onTagAdd'));
    }
    
    public function init()
    {
        if ( !$this->isActive() ) return;
        
        $this->genericInit();
        
        OW::getEventManager()->bind('feed.on_item_render', array($this, 'onItemRender'));
    }
}