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
class EQUESTIONS_CMP_UserSelector extends OW_Component
{

    private $uniqId, $delegate, $entityId, $userId, $friendsMode = false;

    public function __construct( $delegate, $entityId = null, $allMode = null )
    {
        parent::__construct();

        $this->uniqId = uniqid('ul');
        $this->delegate = $delegate;
        $this->userId = OW::getUser()->getId();
        $this->entityId = $entityId;
        $this->friendsMode = $allMode === null ? (bool) OW::getEventManager()->call('plugin.friends') : false;
    }

    public function initJs( $cacheList, $allCount )
    {
        $glob = array(
            'rsp' => OW::getRouter()->urlFor('EQUESTIONS_CTRL_Common', 'rsp'),
            'delegate' => $this->delegate
        );

        $data = array(
            'entityId' => $this->entityId,
            'ajaxMode' => count($cacheList) < $allCount,
            'friendsMode' => $this->friendsMode
        );

        $js = UTIL_JsGenerator::newInstance()->newObject(
                array('CORE.ObjectRegistry', $this->uniqId),
                'UI.UserSelector',
                array(
                    $this->uniqId,
                    $data,
                    $glob,
                    $cacheList
                ));

        OW::getDocument()->addOnloadScript($js);
    }

    public function getList()
    {
        $users = null;
        $count = null;

        if ( $this->friendsMode )
        {
            $count = OW::getEventManager()->call('plugin.friends.count_friends', array(
                'userId' => $this->userId,
            ));

            $users = OW::getEventManager()->call('plugin.friends.get_friend_list', array(
                'userId' => $this->userId,
                'count' => 500
            ));
        }

        $hideUsers = array();

        if ( !empty($this->entityId) )
        {
            $sentNotifications = EQUESTIONS_BOL_NotificationService::getInstance()->findSentNotificationList($this->entityId, $this->userId, 'ask');

            foreach ( $sentNotifications as $notification )
            {
                $hideUsers[] = $notification->userId;
            }
        }

        if ( $count === null )
        {
            $count = BOL_UserService::getInstance()->count();
        }

        if ( $users === null )
        {
            $users = array();
            $userDtos = BOL_UserService::getInstance()->findRecentlyActiveList(0, 500);

            foreach ( $userDtos as $u )
            {
                if ( $u->id != $this->userId )
                {
                    $users[] = $u->id;
                }
            }
        }

        $out = array();
        foreach ( $users as $user )
        {
            if ( !in_array($user, $hideUsers) )
            {
                $out[] = $user;
            }
        }

        return array(
            'count' => $count -1,
            'idList' => $out
        );
    }

    public function onBeforeRender()
    {
        parent::onBeforeRender();

        $displayCount = 60;

        $list = $this->getList();
        $idList = empty($list['idList']) ? array() : $list['idList'];

        $allList = BOL_AvatarService::getInstance()->getDataForUserAvatars($idList, true, false, true, false);

        $allList = empty($allList) ? array() : $allList;

        $cachelist = array();
        foreach ( $allList as $uid => $info )
        {
            $info['userId'] = $uid;
            $info['kw'] = strtolower($info['title']);
            $cachelist[$uid] = $info;
        }

        $tplList = array_slice($cachelist, 0, $displayCount);

        $this->initJs($cachelist, $list['count'] );

        $this->assign('list', $tplList);
        $this->assign('friendsMode', $this->friendsMode);

        $this->assign('uniqId', $this->uniqId);

        $language = OW::getLanguage();
        $this->assign('langs', array(
            'ask' => $language->text('equestions', 'user_select_button_ask')
        ));

        $this->assign('fakeAvatar', array(
            'src' => '-',
            'title' => '-'
        ));

        $moderator = OW::getUser()->isAuthorized('equestions');
        $this->assign('moderator', $moderator);
    }
}