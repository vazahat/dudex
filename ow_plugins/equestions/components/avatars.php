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
 * @package equestions.components
 */
class EQUESTIONS_CMP_Avatars extends OW_Component
{

    /**
     * Constructor.
     *
     * @param array $idList
     */
    public function __construct( array $idList, $totalCount )
    {
        parent::__construct();

        $userId = OW::getUser()->getId();
        $hiddenUser = false;
        if ( $userId && !in_array($userId, $idList) )
        {
            $hiddenUser = $userId;
            $idList[] = $userId;
        }

        $users = BOL_AvatarService::getInstance()->getDataForUserAvatars($idList, true, true, true, false);

        if ($hiddenUser)
        {
            $users[$hiddenUser]['id'] = $hiddenUser;
            $this->assign('hiddenUser', $users[$hiddenUser]);
            unset($users[$hiddenUser]);
        }

        $count = count($users);
        $otherCount = $totalCount - ($count > 3 ? 3 : $count);
        $otherCount = $otherCount < 0 ? 0 : $otherCount;

        $this->assign('otherCount', $otherCount);

        $this->assign('users', $users);

        $staticUrl = OW::getPluginManager()->getPlugin('equestions')->getStaticUrl();
        $this->assign('staticUrl', $staticUrl);

    }

}