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
class UTAGS_CLASS_AbstractUserBridge
{
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
            
            $itemCmp = new UTAGS_CMP_UserItem($data);
            
            $item = array();
            $item["id"] = UTAGS_BOL_Service::ENTITY_TYPE_USER . "_" . $userId;
            $item["text"] = $data['text'];
            $item['html'] = $itemCmp->render();
            $item['url'] = $data['url'];
            $item['count'] = null;

            if ( !empty($group) ) {
                $item['group'] = $group;
            }

            $out[UTAGS_BOL_Service::ENTITY_TYPE_USER . '_' . $userId] = $item;
        }

        return $out;
    }
    
    public function init()
    {
        
    }
}
