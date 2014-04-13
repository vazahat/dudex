<?php

/**
 * Copyright (c) 2012, Sergey Kambalin
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 *
 * @author Sergey Kambalin <greyexpert@gmail.com>
 * @package mcompose.class
 * @since 1.0
 */
class MCOMPOSE_CLASS_AbstractUserBridge
{
    const ID_PREFIX = "user";
    
    protected function getInfoList( $userIdList )
    {
        $fields = array();

        $qs = array();

        $qBdate = BOL_QuestionService::getInstance()->findQuestionByName('birthdate');

        if ( $qBdate->onView )
            $qs[] = 'birthdate';

        $qSex = BOL_QuestionService::getInstance()->findQuestionByName('sex');

        if ( $qSex->onView )
            $qs[] = 'sex';

        $questionList = BOL_QuestionService::getInstance()->getQuestionData($userIdList, $qs);

        foreach ( $questionList as $uid => $q )
        {
            $info = array();

            if ( !empty($q['sex']) )
            {
                $info['sex'] = BOL_QuestionService::getInstance()->getQuestionValueLang('sex', $q['sex']);
            }

            if( !empty($q['birthdate']) )
            {
                $date = UTIL_DateTime::parseDate($q['birthdate'], UTIL_DateTime::MYSQL_DATETIME_DATE_FORMAT);
                $age = UTIL_DateTime::getAge($date['year'], $date['month'], $date['day']);
                $info['age'] = $age;
            }

            $fields[$uid] = $info;
        }

        return $fields;
    }
    
    protected function buildData( $userIds, $group = null, $ignoreUserIds = array() )
    {
        if ( empty($userIds) )
        {
            return array();
        }

        $avatarData = BOL_AvatarService::getInstance()->getDataForUserAvatars($userIds, true, true, true, false);
        $infoList = $this->getInfoList($userIds);
        $onlineList = BOL_UserService::getInstance()->findOnlineStatusForUserList($userIds);

        $out = array();

        foreach ( $userIds as $userId )
        {
            if ( in_array($userId, $ignoreUserIds) )
            {
                continue;
            }

            $data = array();
            $data['id'] = $userId;
            $data['url'] = $avatarData[$userId]['url'];
            $data['avatar'] = $avatarData[$userId]['src'];
            $data['text'] = $avatarData[$userId]['title'];
            $data['info'] = '<span class="ow_live_on"></span>' . implode(' ', $infoList[$userId]);
            $data['online'] = $onlineList[$userId];
            
            $itemCmp = new MCOMPOSE_CMP_UserItem($data);
            
            $item = array();
            $item["id"] = self::ID_PREFIX . "_" . $userId;
            $item["text"] = $data['text'];
            $item['html'] = $itemCmp->render();
            $item['url'] = $data['url'];
            $item['count'] = null;

            if ( !empty($group) ) {
                $item['group'] = $group;
            }

            $out[self::ID_PREFIX . '_' . $userId] = $item;
        }

        return $out;
    }
    
    public function onSend( BASE_CLASS_EventCollector $event )
    {
        $params = $event->getParams();
        $recipients = $params["recipients"];
        
        foreach ( $recipients as $r )
        {
            list($prefix, $id) = explode("_", $r);
            
            if ( $prefix == self::ID_PREFIX )
            {
                $event->add($id);
            }
        }
    }
    
    public function init()
    {
        static $calledOnce = false;
        
        if ( !$calledOnce )
        {
            OW::getEventManager()->bind(MCOMPOSE_BOL_Service::EVENT_ON_SEND, array($this, "onSend"));
            $calledOnce = true;
        }
    }
}
