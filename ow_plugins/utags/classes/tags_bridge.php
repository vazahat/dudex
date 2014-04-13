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
class UTAGS_CLASS_TagsBridge
{
    const ENTITY_PHOTO = "photo";
    
    /**
     * Class instance
     *
     * @var UTAGS_CLASS_TagsBridge
     */
    private static $classInstance;

    /**
     * Returns class instance
     *
     * @return UTAGS_CLASS_TagsBridge
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
     * @var BOL_TagService
     */
    private $tagService;
    
    public function __construct()
    {
        $this->tagService = BOL_TagService::getInstance();
    }
    
    private function fetchTags( $list )
    {
        $out = array();
        foreach ( $list as $tag )
        {
            $out[] = array(

                "id" => $tag["id"],
                "text" => $tag["label"],
                "count" => empty($tag["count"]) ? null : $tag["count"]
            );
        }
        
        return $out;
    }
    
    public function getPopularTags()
    {
        $list = $this->tagService->findMostPopularTags(self::ENTITY_PHOTO, 500);
        
        $out = array();
        foreach ( $list as $tag )
        {
            $out[] = array(

                "id" => $tag["id"],
                "text" => $tag["label"],
                "count" => empty($tag["count"]) ? null : $tag["count"]
            );
        }
        
        return $out;
    }
    
    /**
     * 
     * @param int $photoId
     * @param int $tagText
     * @return BOL_EntityTag
     */
    public function addTag( $photoId, $tagText )
    {
        $tags = BOL_TagDao::getInstance()->findTagsByLabel(array($tagText));
        
        if ( empty($tags) )
        {
            $tag = new BOL_Tag;
            $tag->label = $tagText;
            BOL_TagDao::getInstance()->save($tag);
        }
        else 
        {
            $tag = $tags[0];
        }
        
        $entityTagItem = new BOL_EntityTag();
        $entityTagItem->setEntityId($photoId)->setEntityType(self::ENTITY_PHOTO)->setTagId($tag->getId());
        
        BOL_EntityTagDao::getInstance()->save($entityTagItem);
        
        return $entityTagItem;
    }
    
    public function beforeTagAdd( OW_Event $event )
    {
        $params = $event->getParams();
        /*@var $tag UTAGS_BOL_Tag */
        $tag = $params['tag'];
        $tagData = $tag->getData();
        
        if ( $tag->entityType != UTAGS_BOL_Service::ENTITY_TYPE_CUSTOM )
        {
            return;
        }
        
        if ( empty($tagData["data"]["text"]) )
        {
            return;
        }
        
        $tagEntity = $this->addTag($tag->photoId, $tagData["data"]["text"]);
        $tag->entityId = $tagEntity->id;
    }
    
    public function beforeTagDelete( OW_Event $event )
    {
        $params = $event->getParams();
        $tagId = $params['tagId'];

        $tag = UTAGS_BOL_Service::getInstance()->findTagById($tagId);
        
        if ( empty($tagId) || $tag->entityType != UTAGS_BOL_Service::ENTITY_TYPE_CUSTOM )
        {
            return;
        }
        
        BOL_EntityTagDao::getInstance()->deleteById($tag->entityId);
    }
    
    
    public function getPhotoTags( $photoId )
    {
        $list = $this->tagService->findEntityTags($photoId, self::ENTITY_PHOTO);
        
        $out = array();
        foreach ( $list as $tag )
        {
            /*@var $tag BOL_Tag */
            $out[] = array(
                "id" => $tag->id,
                "text" => $tag->label,
                "count" => null
            );
        }
        
        return $out;
    }
    
    
    protected function buildData( $tags, $group = null )
    {
        if ( empty($tags) )
        {
            return array();
        }

        $out = array();

        foreach ( $tags as $tag )
        {
            $itemCmp = new UTAGS_CMP_TagItem($tag);
            
            $item = array();
            $item["id"] = UTAGS_BOL_Service::ENTITY_TYPE_CUSTOM . "_" . $tag["id"];
            $item["text"] = $tag['text'];
            $item['html'] = $itemCmp->render();

            if ( !empty($group) ) {
                $item['group'] = $group;
            }

            $out[UTAGS_BOL_Service::ENTITY_TYPE_CUSTOM . '_' . $tag["id"]] = $item;
        }

        return $out;
    }
    
    public function onSearch( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        
        $kw = $params["kw"];
        $userId = $params["userId"];
        $context = $params["context"];
        $photoId = empty($params["contextId"]) ? null : $params["contextId"];
        
        if ( $kw === null )
        {
            return;
        }
        
        $popularTags = $this->buildData($this->getPopularTags(), OW::getLanguage()->text('utags', 'selector_group_tags'));
        
        foreach ($popularTags as $item )
        {
            $event->add($item);
        }
    }
    
    public function onInputInit( OW_Event $event )
    {
        $params = $event->getParams();
        
        /* @var $input UTAGS_CMP_Tags */
        $input = $params["input"];
        
        $input->setupGroup(OW::getLanguage()->text('utags', 'selector_group_tags'), array(
            'priority' => 8,
            'alwaysVisible' => true,
            'noMatchMessage' => false
        ));
    }

    public function genericInit()
    {
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_BEFORE_ADD, array($this, "beforeTagAdd"));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_BEFORE_DELETE, array($this, 'beforeTagDelete'));
    }
    
    public function init()
    {
        $this->genericInit();
        
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_ON_SEARCH, array($this, "onSearch"));
        OW::getEventManager()->bind(UTAGS_BOL_Service::EVENT_ON_INPUT_INIT, array($this, "onInputInit"));
    }
}