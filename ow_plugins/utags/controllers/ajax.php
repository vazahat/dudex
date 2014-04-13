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
 * @package utags.controllers
 */
class UTAGS_CTRL_Ajax extends OW_ActionController
{
    /**
     *
     * @var UTAGS_BOL_Service
     */
    private $service;
    
    public function init() 
    {
        parent::init();
        
        $this->service = UTAGS_BOL_Service::getInstance();
    }
    
    public function searchRsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect403Exception;
        }

        if ( !OW::getUser()->isAuthenticated() )
        {
            echo json_encode(array());
            exit;
        }

        $kw = $_GET['term'];
        $context = empty($_GET["context"]) ? "photo" : $_GET["context"];
        $contextId = empty($_GET["contextId"]) ? null : $_GET["contextId"];
        $userId = OW::getUser()->getId();

        $entries = UTAGS_BOL_Service::getInstance()->getSuggestEntries($userId, $kw, $context, $contextId);

        echo json_encode($entries);
        exit;
    }
    
    public function rsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        $command = trim($_GET['command']);
        $query = json_decode($_GET['params'], true);

        try
        {
            $response = call_user_func(array($this, $command), $query);
        }
        catch ( InvalidArgumentException $e )
        {
            $response = array(
                'type' => 'error',
                'error' => $e->getMessage()
            );
        }

        $response = empty($response) ? array() : $response;
        echo json_encode($response);
        exit;
    }
    
    private function renderTagList( $photoId )
    {
        $tags = $this->service->getTagsByPhotoId($photoId);
        if ( empty($tags) )
        {
            return null;
        }
        
        $cmp = new UTAGS_CMP_TagList($tags);
        
        return trim($cmp->render());
    }
    
    private function getPermissions()
    {
        $permissions = array();
        $permissions["credits"]["actions"] = UTAGS_CLASS_CreditsBridge::getInstance()->getAllPermissions();
        $permissions["credits"]["messages"] = UTAGS_CLASS_CreditsBridge::getInstance()->getAllPermissionMessages();
        $permissions["isModerator"] = OW::getUser()->isAuthorized("utags");
        
        return $permissions;
    }
    
    
    private function stopTagging( $params )
    {
        // Skip
    }
    
    private function saveTags( $params ) {
        
        $photoId = $params["photoId"];
        $tagsData = $params["tags"];
        $tagIds = array();
        
        foreach ( $tagsData as $tagData )
        {
            $tag = null;
            if ( !empty($tagData["id"]) )
            {
               $tag = $this->service->findTagById($tagData["id"]);
            }
            
            $newTag = false;
            
            if ( $tag === null )
            {
                $tag = new UTAGS_BOL_Tag;
                $tag->photoId = $photoId;
                $newTag = true;
            }
            
            list($entityType, $entityId) = explode("_", $tagData["data"]["id"]);
            if ( $entityType == "custom" )
            {
                $entityId = uniqid();
            }

            $tag->entityType = $entityType;
            $tag->entityId = $entityId;
            
            $tag->userId = OW::getUser()->getId();
            $tag->timeStamp = time();
            $tag->status = UTAGS_BOL_Tag::STATUS_ACTIVE;
            $tag->setData($tagData);
            
            if ( $newTag )
            {
                $this->service->addTag($tag);
            }
            else
            {
                $this->service->saveTag($tag);
            }
            
            $tagIds[$tagData["cid"]] = $tag->id;
        }
        
        return array(
            "tags" => $tagIds,
            "list" => $this->renderTagList($photoId),
            "permissions" => $this->getPermissions(),
            "customList" => UTAGS_CLASS_PhotoBridge::getInstance()->getTagCloudHtml($photoId),
            "clearCache" => true
        );
    }
    
    private function fetchTags( $params )
    {
        $photoId = $params["photoId"];
        
        $tags = $this->service->getTagsByPhotoId($photoId);
        $tagList = array();
        
        foreach ( $tags as $tag )
        {
            /* @var $tag UTAGS_BOL_Tag */
            $_tag = $tag->getData();
            $_tag["id"] = $tag->id;
            $_tag["remove"] = $this->service->isCurrentUserCanDelete($tag);
            
            $tagList[] = $_tag;
        }
        
        return array(
            "tags" => $tagList,
            "list" => $this->renderTagList($photoId),
        );
    }
    
    private function deleteTags( $params )
    {
        $photoId = $params["photoId"];
        $tagIds = $params["tagIds"];
        
        $clonnedPhoto = false;
        
        foreach ( $tagIds as $tagId )
        {
            $tag = $this->service->findTagById($tagId);
            if ( $tag->copyPhotoId == $photoId )
            {
                $clonnedPhoto = true;
            }
            
            $this->service->deleteTagById($tagId);
        }
        
        return array(
            "list" => $this->renderTagList($photoId),
            "customList" => UTAGS_CLASS_PhotoBridge::getInstance()->getTagCloudHtml($photoId),
            "clearCache" => true,
            "close" => $clonnedPhoto,
            "refresh" => $clonnedPhoto
        );
    }
}
