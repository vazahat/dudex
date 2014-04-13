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
 * @package utags.components
 */
class UTAGS_CMP_TagList extends OW_Component
{
    public function __construct( $tags ) 
    {
        parent::__construct();
        
        $avatarService = BOL_AvatarService::getInstance();
        
        $tplTags = array();
        foreach ( $tags as $tag )
        {
            /*@var $tag UTAGS_BOL_Tag */
            if ( $tag->entityType != "user" ) {
                continue;
            }
            
            $avatarData = $avatarService->getDataForUserAvatars(array($tag->entityId, $tag->userId), true, true, true, false);
            $userAvatarData = $avatarData[$tag->entityId];
            $taggerAvatarData = $avatarData[$tag->userId];
            
            $tplTags[] = array(
                "avatar" => $userAvatarData,
                "taggerAvatar" => $taggerAvatarData,
                "userId" => $tag->entityId,
                "taggerId" => $tag->userId,
                "id" => $tag->id,
                "delete" => UTAGS_BOL_Service::getInstance()->isCurrentUserCanDelete($tag)
            );
        }
        
        $this->assign("tags", $tplTags);
    }
}
