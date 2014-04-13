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
 * @package equestions.controllers
 */
class EQUESTIONS_CTRL_Common extends OW_ActionController
{
    public function rsp()
    {
        if ( !OW::getRequest()->isAjax() )
        {
            throw new Redirect404Exception();
        }

        $command = trim($_POST['command']);
        $query = json_decode($_POST['params'], true);

        $responce = call_user_func(array($this, $command), $query);

        echo json_encode($responce);
        exit;
    }

    public function userSearch( $query )
    {
        $kw = $query['kw'];
        $data = $query['data'];
        $friendMode = $data['friendsMode'];

        $idList = array();

        $userIds = array();

        if ( $friendMode )
        {
            if ( OW::getUser()->isAuthenticated() )
            {
                $userId = OW::getUser()->getId();

                $userIds = EQUESTIONS_BOL_Service::getInstance()->findFriends($kw, $userId);
            }
        }
        else
        {
            $userIds = EQUESTIONS_BOL_Service::getInstance()->findUsers($kw);
        }

        foreach ( $userIds as $u )
        {
            if ( $u != OW::getUser()->getId() )
            {
                $idList[] = $u;
            }
        }

        $allList = empty($idList) ? array() : BOL_AvatarService::getInstance()->getDataForUserAvatars($idList, true, false, true, false);

        $cachelist = array();
        foreach ( $allList as $uid => $info )
        {
            $info['userId'] = $uid;
            $info['kw'] = strtolower($info['title']);
            $cachelist[$uid] = $info;
        }

        return $cachelist;
    }
}