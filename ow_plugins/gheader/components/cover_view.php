<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package gheader.components
 */
class GHEADER_CMP_CoverView extends OW_Component
{
    const MIN_HEIGHT = 400;

    public function __construct( $groupId )
    {
        parent::__construct();

        $cover = GHEADER_BOL_Service::getInstance()->findCoverByGroupId($groupId);

        if ( empty($cover) )
        {
            $this->assign('error', OW::getLanguage()->text('gheader', 'cover_not_found'));

            return;
        }

        $group = GROUPS_BOL_Service::getInstance()->findGroupById($cover->groupId);
        
        $src = GHEADER_BOL_Service::getInstance()->getCoverUrl($cover);

        $settings = $cover->getSettings();
        $height = $settings['dimensions']['height'];
        $width = $settings['dimensions']['width'];

        $top = 0;

        if ( $height < self::MIN_HEIGHT )
        {
            $top = (self::MIN_HEIGHT - $height) / 2;
        }

        $this->assign('src', $src);
        $this->assign('top', $top);
        $this->assign('dimensions', $settings['dimensions']);

        $userId = OW::getUser()->getId();
        
        $cmtParams = new BASE_CommentsParams('gheader', GHEADER_CLASS_CommentsBridge::ENTITY_TYPE);
        $cmtParams->setEntityId($cover->id);
        $cmtParams->setAddComment(GHEADER_BOL_Service::getInstance()->isUserCanInteract($userId, $group->id));
        $cmtParams->setOwnerId($group->userId);
        $cmtParams->setDisplayType(BASE_CommentsParams::DISPLAY_TYPE_TOP_FORM_WITH_PAGING);

        $photoCmts = new BASE_CMP_Comments($cmtParams);
        $this->addComponent('comments', $photoCmts);
    }
}