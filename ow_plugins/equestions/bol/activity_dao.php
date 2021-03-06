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
 * @package equestions.bol
 */
class EQUESTIONS_BOL_ActivityDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_BOL_ActivityDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_BOL_ActivityDao
     */
    public static function getInstance()
    {
        if ( self::$classInstance === null )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'EQUESTIONS_BOL_Activity';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'equestions_activity';
    }

    /**
     *
     * @param int $questionId
     * @param string $activityType
     * @param int $activityId
     * @return EQUESTIONS_BOL_Activity
     */
    public function findActivity( $questionId, $activityType, $activityId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $questionId);
        $example->andFieldEqual('activityType', $activityType);
        $example->andFieldEqual('activityId', $activityId);

        return $this->findObjectByExample($example);
    }

    public function deleteActivity( $questionId, $activityType, $activityId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $questionId);
        $example->andFieldEqual('activityType', $activityType);
        $example->andFieldEqual('activityId', $activityId);

        return $this->deleteByExample($example);
    }

    public function findMainActivity( $startStamp, $questionIds )
    {
        if ( empty($questionIds) )
        {
            return array();
        }

        $questionsIN = implode(',', $questionIds);

        $query = "SELECT a.* FROM " . $this->getTableName() . " a
            WHERE a.privacy=:pe AND a.timeStamp <= :ss AND a.questionId IN ($questionsIN)
            ORDER BY a.timeStamp DESC";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'pe' => EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY
        ));
    }

    public function findMyActivity( $startStamp, $questionIds, $userId )
    {
        if ( empty($questionIds) )
        {
            return array();
        }

        $questionsIN = implode(',', $questionIds);

        $query = "SELECT a.* FROM " . $this->getTableName() . " a
            WHERE a.privacy!=:pn AND a.timeStamp <= :ss AND a.questionId IN ($questionsIN)
            ORDER BY a.timeStamp DESC";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findFriendsActivity( $startStamp, $questionIds, $userId )
    {
        if ( empty($questionIds) )
        {
            return array();
        }

        $friendsDao = FRIENDS_BOL_FriendshipDao::getInstance();

        $questionsIN = implode(',', $questionIds);

        $query = "SELECT a.* FROM " . $this->getTableName() . " a
            INNER JOIN " . $friendsDao->getTableName() . " f ON ( a.userId=f.userId OR a.userId=f.friendId ) AND f.status=:fs
            WHERE a.privacy!=:pn AND a.timeStamp <= :ss AND a.questionId IN ($questionsIN) AND ( f.userId =:u OR f.friendId=:u )
            ORDER BY a.timeStamp DESC";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'fs' => FRIENDS_BOL_FriendshipDao::VAL_STATUS_ACTIVE,
            'u' => $userId,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findNotificationsActivity( $startStamp, $questionIds, $userId )
    {
        if ( empty($questionIds) )
        {
            return array();
        }

        $questionsIN = implode(',', $questionIds);

        $query = "SELECT a.* FROM " . $this->getTableName() . " a
            WHERE a.timeStamp <= :ss AND a.activityType=:aa AND a.activityId=:u AND a.questionId IN ($questionsIN)
            ORDER BY a.timeStamp DESC";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'u' => $userId,
            'ss' => $startStamp,
            'aa' => EQUESTIONS_BOL_FeedService::ACTIVITY_ASK
        ));
    }

    public function setPrivacy( $userId, $privacy )
    {
        $query = 'UPDATE ' . $this->getTableName() . ' SET privacy=:p WHERE userId=:u';

        $this->dbo->query($query, array(
            'p' => $privacy,
            'u' => $userId
        ));
    }
}