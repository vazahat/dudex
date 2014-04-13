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
 * @package utags.bol
 */
class UTAGS_BOL_Service
{
    const EVENT_ON_SEARCH = "utags.on_search";
    const EVENT_ON_INPUT_INIT = "utags.on_input_init";
    
    const EVENT_BEFORE_ADD = "utags.before_add";
    const EVENT_AFTER_ADD = "utags.after_add";
    
    const EVENT_BEFORE_UPDATE = "utags.before_update";
    const EVENT_AFTER_UPDATE = "utags.after_update";
    
    const EVENT_BEFORE_DELETE = "utags.before_delete";
    const EVENT_AFTER_DELETE = "utags.after_delete";
    
    const ENTITY_TYPE_USER = "user";
    const ENTITY_TYPE_CUSTOM = "custom";
    
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UTAGS_BOL_Service
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }
    
    /**
     *
     * @var UTAGS_BOL_TagDao
     */
    private $tagDao;

    private function __construct()
    {
        $this->tagDao = UTAGS_BOL_TagDao::getInstance();
    }
    
    /**
     * 
     * @param UTAGS_BOL_Tag $tag
     * @return UTAGS_BOL_Tag
     */
    public function saveTag( UTAGS_BOL_Tag $tag )
    {
        $event = new OW_Event(self::EVENT_BEFORE_UPDATE, array(
            "tag" => $tag
        ));
        OW::getEventManager()->trigger($event);
        
        $this->tagDao->save($tag);
        
        $event = new OW_Event(self::EVENT_AFTER_UPDATE, array(
            "tag" => $tag
        ));
        OW::getEventManager()->trigger($event);
        
        return $tag;
    }
    
    /**
     * 
     * @param UTAGS_BOL_Tag $tag
     * @return UTAGS_BOL_Tag
     */
    public function addTag( UTAGS_BOL_Tag $tag )
    {
        $event = new OW_Event(self::EVENT_BEFORE_ADD, array(
            "tag" => $tag
        ));
        OW::getEventManager()->trigger($event);
        
        $this->tagDao->save($tag);
        
        $event = new OW_Event(self::EVENT_AFTER_ADD, array(
            "tag" => $tag
        ));
        OW::getEventManager()->trigger($event);
        
        return $tag;
    }
    
    public function deleteTagById( $id )
    {
        $event = new OW_Event(self::EVENT_BEFORE_DELETE, array(
            "tagId" => $id
        ));
        OW::getEventManager()->trigger($event);
        
        $this->tagDao->deleteById($id);
        
        $event = new OW_Event(self::EVENT_AFTER_DELETE, array(
            "tagId" => $id
        ));
        OW::getEventManager()->trigger($event);
    }
    
    public function deleteByPhotoId( $photoId )
    {
        $tags = $this->tagDao->findByPhotoId($photoId);
        
        foreach ( $tags as $tag )
        {
            $this->deleteTagById($tag->id);
        }
    }
    
    public function deleteByEntity( $entityType, $entityId )
    {
        $tags = $this->findTagsByEntity($entityType, $entityId);
        
        foreach ( $tags as $tag )
        {
            $this->deleteTagById($tag->id);
        }
    }
    
    private function fetchTagList( $list )
    {
        $out = array();
        foreach ( $list as $tag )
        {
            $out[$tag->id] = $tag;
        }
        
        return $out;
    }
    
    public function isCurrentUserCanDelete( UTAGS_BOL_Tag $tag )
    {
        if ( OW::getUser()->isAuthorized("utags") )
        {
            return true;
        }
        
        $userId = OW::getUser()->getId();
        $photoOwnerId = UTAGS_CLASS_PhotoBridge::getInstance()->getPhotoOwnerId($tag->photoId);
        
        return $tag->userId == $userId || $photoOwnerId == $userId || ( $tag->entityType == self::ENTITY_TYPE_USER && $tag->entityId == $userId );
    }
    
    public function getTagsByPhotoId( $photoId, $type = null )
    {
        $tagList = $this->tagDao->findByPhotoIdOrCopyPhotoId($photoId, $type);
        
        $photoOwnerId = UTAGS_CLASS_PhotoBridge::getInstance()->getPhotoOwnerId($photoId);
        $userId = OW::getUser()->getId();
        
        $userTags = array();
        
        foreach ( $tagList as $tag )
        {
            $userTags[$tag->userId] = isset($userTags[$tag->userId]) 
                ? $userTags[$tag->userId] 
                : array();
            
            $userTags[$tag->userId][] = $tag;
        }
        
        $actions = @OW::getEventManager()->call("privacy_check_permission_for_user_list", array(
            "action" => UTAGS_CLASS_PrivacyBridge::ACTION_VIEW_TAGS,
            "ownerIdList" => array_keys($userTags),
            "viewerId" => OW::getUser()->getId()
        ));
        
        $out = array();
        
        $globalPermited = OW::getUser()->isAuthorized("utags", "view_tags");
        $moderatorPermited = OW::getUser()->isAuthorized("utags");
        
        foreach ( $tagList as $tag )
        {
            $permited = false;
            
            /*@var $tag UTAGS_BOL_Tag */
            
            if ( $moderatorPermited )
            {
                $permited = true;
            }
            else
            {
                if ( $tag->userId == $userId || $photoOwnerId == $userId || ( $tag->entityType == self::ENTITY_TYPE_USER && $tag->entityId == $userId ) )
                {
                    $permited = true;
                }
                else if ( !$actions[$tag->userId]["blocked"] )
                {
                    $permited = true;
                }
                else
                {
                    $permited = $globalPermited;
                }
            }
            
            if ( $permited )
            {
                $out[] = $tag;
            }
        }
        
        return $out;
    }
    
    public function findTagsByPhotoIdAndEntity( $photoId, $entityType, $entityId )
    {
        return $this->tagDao->findByPhotoIdAndEntity($photoId, $entityType, $entityId);
    }
    
    public function findTagsByCopyPhotoIdAndEntity( $photoId, $entityType, $entityId )
    {
        return $this->tagDao->findByCopyPhotoIdAndEntity($photoId, $entityType, $entityId);
    }
    
    public function findTagsByCopyPhotoId( $photoId )
    {
        return $this->tagDao->findByCopyPhotoId($photoId);
    }
    
    public function findTagsByPhotoId( $photoId )
    {
        return $this->tagDao->findByPhotoId($photoId);
    }
    
    /**
     * 
     * @param int $id
     * @return UTAGS_BOL_Tag
     */
    public function findTagById( $id )
    {
        return $this->tagDao->findById($id);
    }
    
    public function getSuggestEntries( $userId, $kw = null, $context = "photo", $contextId = null )
    {
        $event = new BASE_CLASS_EventCollector(self::EVENT_ON_SEARCH, array(
            "kw" => $kw,
            "userId" => $userId,
            "context" => $context,
            "contextId" => $contextId
        ));
        
        OW::getEventManager()->trigger($event);
        
        $out = array();
        
        foreach ( $event->getData() as $item )
        {
            $out[$item["id"]] = $item;
        }
        
        return $out;
    }
}
