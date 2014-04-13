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
class EQUESTIONS_BOL_QuestionDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_BOL_QuestionDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_BOL_QuestionDao
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
        return 'EQUESTIONS_BOL_Question';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'equestions_question';
    }

    public function findMainFeed( $startStamp, $count, $questionIds )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();

        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            INNER JOIN " . $activityDao->getTableName() . " c ON a.questionId=c.questionId AND c.activityType=:ac
            WHERE a.timeStamp <= :ss AND c.privacy=:pe AND a.privacy=:pe AND $in
            GROUP BY q.id
            ORDER BY MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'ac' => EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE,
            'pe' => EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY,
            'c' => (int) $count
        ));
    }

    public function findOrderedMainFeed( $startStamp, $count, $questionIds, $orderActivities )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();


        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';
        $orderWhere = '1';
        if ( !empty($orderActivities) )
        {
            $orderActivities[] = EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE;
            $orderWhere = "a.activityType IN ('" . implode("','", $orderActivities) . "')";
        }

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            INNER JOIN " . $activityDao->getTableName() . " c ON a.questionId=c.questionId AND c.activityType=:ac
            WHERE a.timeStamp <= :ss AND c.privacy=:pe AND a.privacy=:pe AND $in AND $orderWhere
            GROUP BY q.id
            ORDER BY COUNT(a.id) DESC, MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'ac' => EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE,
            'pe' => EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY,
            'c' => (int) $count
        ));
    }

    public function findMainFeedCount( $startStamp )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();

        $query = "SELECT COUNT(DISTINCT q.id) FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            INNER JOIN " . $activityDao->getTableName() . " c ON a.questionId=c.questionId AND c.activityType=:ac
            WHERE a.timeStamp <= :ss AND c.privacy=:pe AND a.privacy=:pe";

        return $this->dbo->queryForColumn($query, array(
            'ss' => $startStamp,
            'ac' => EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE,
            'pe' => EQUESTIONS_BOL_FeedService::PRIVACY_EVERYBODY
        ));
    }

    public function findMyFeed( $startStamp, $userId, $count, $questionIds )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $followDao = EQUESTIONS_BOL_FollowDao::getInstance();

        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            LEFT JOIN " . $followDao->getTableName() . " f ON q.id=f.questionId
            WHERE a.privacy!=:pn AND a.timeStamp <= :ss AND $in
                AND ( f.userId=:u OR a.userId=:u )

            GROUP BY q.id
            ORDER BY MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'u' => $userId,
            'c' => (int) $count,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findOrderedMyFeed( $startStamp, $userId, $count, $questionIds, $orderActivities )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $followDao = EQUESTIONS_BOL_FollowDao::getInstance();

        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';
        $orderWhere = '1';
        if ( !empty($orderActivities) )
        {
            $orderActivities[] = EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE;
            $orderWhere = "a.activityType IN ('" . implode("','", $orderActivities) . "')";
        }

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            LEFT JOIN " . $followDao->getTableName() . " f ON q.id=f.questionId
            WHERE a.privacy!=:pn AND a.timeStamp <= :ss AND $in AND $orderWhere
                AND ( f.userId=:u OR a.userId=:u )

            GROUP BY q.id
            ORDER BY COUNT(a.id) DESC, MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'u' => $userId,
            'c' => (int) $count,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findMyFeedCount( $startStamp, $userId )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $followDao = EQUESTIONS_BOL_FollowDao::getInstance();

        $query = "SELECT COUNT(DISTINCT q.id) FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            LEFT JOIN " . $followDao->getTableName() . " f ON q.id=f.questionId
            WHERE a.privacy!=:pn AND a.timeStamp <= :ss
                AND f.userId=:u
                OR a.userId=:u";

        return $this->dbo->queryForColumn($query, array(
            'ss' => $startStamp,
            'u' => $userId,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }


    public function findFriendsFeed( $startStamp, $userId, $count, $questionIds )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $friendsDao = FRIENDS_BOL_FriendshipDao::getInstance();

        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            INNER JOIN " . $friendsDao->getTableName() . " f ON ( a.userId=f.userId OR a.userId=f.friendId ) AND f.status=:fs
            WHERE a.timeStamp <= :ss AND $in
                AND ( f.userId =:u OR f.friendId=:u )
                AND a.userId!=:u
                AND a.privacy!=:pn

            GROUP BY q.id
            ORDER BY MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'u' => $userId,
            'fs' => FRIENDS_BOL_FriendshipDao::VAL_STATUS_ACTIVE,
            'c' => (int) $count,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findOrderedFriendsFeed( $startStamp, $userId, $count, $questionIds, $orderActivities )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $friendsDao = FRIENDS_BOL_FriendshipDao::getInstance();

        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';
        $orderWhere = '1';
        if ( !empty($orderActivities) )
        {
            $orderActivities[] = EQUESTIONS_BOL_FeedService::ACTIVITY_CREATE;
            $orderWhere = "a.activityType IN ('" . implode("','", $orderActivities) . "')";
        }

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            INNER JOIN " . $friendsDao->getTableName() . " f ON ( a.userId=f.userId OR a.userId=f.friendId ) AND f.status=:fs
            WHERE a.timeStamp <= :ss AND $in AND $orderWhere
                AND ( f.userId =:u OR f.friendId=:u )
                AND a.userId!=:u
                AND a.userId!=:pn

            GROUP BY q.id
            ORDER BY COUNT(a.id) DESC, MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'ss' => $startStamp,
            'u' => $userId,
            'fs' => FRIENDS_BOL_FriendshipDao::VAL_STATUS_ACTIVE,
            'c' => (int) $count,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findFriendsFeedCount( $startStamp, $userId )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();
        $friendsDao = FRIENDS_BOL_FriendshipDao::getInstance();

        $query = "SELECT COUNT(DISTINCT q.id)
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            INNER JOIN " . $friendsDao->getTableName() . " f ON ( a.userId=f.userId OR a.userId=f.friendId ) AND f.status=:fs
            WHERE a.timeStamp <= :ss
                AND ( f.userId =:u OR f.friendId=:u )
                AND a.userId!=:u
                AND a.userId!=:pn";

        return $this->dbo->queryForColumn($query, array(
            'fs' => FRIENDS_BOL_FriendshipDao::VAL_STATUS_ACTIVE,
            'ss' => $startStamp,
            'u' => $userId,
            'pn' => EQUESTIONS_BOL_FeedService::PRIVACY_NOBODY
        ));
    }

    public function findNotificationsFeed( $startStamp, $userId, $count, $questionIds )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();

        $in = empty($questionIds) ? '1' : 'q.id NOT IN (' . implode(',', $questionIds) . ')';

        $query = "SELECT q.*
            FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            WHERE a.timeStamp <= :ss
                AND a.activityType=:aa
                AND a.activityId=:u
                AND $in
            GROUP BY q.id
            ORDER BY MAX(a.timeStamp) DESC
            LIMIT :c";

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'u' => $userId,
            'ss' => $startStamp,
            'aa' => EQUESTIONS_BOL_FeedService::ACTIVITY_ASK,
            'c' => (int) $count
        ));
    }

    public function findNotificationsFeedCount( $startStamp, $userId )
    {
        $activityDao = EQUESTIONS_BOL_ActivityDao::getInstance();

        $query = "SELECT COUNT(DISTINCT q.id) FROM " . $this->getTableName() . " q
            INNER JOIN " . $activityDao->getTableName() . " a ON q.id=a.questionId
            WHERE a.timeStamp <= :ss AND a.activityType=:aa AND a.activityId=:u";

        return $this->dbo->queryForColumn($query, array(
            'u' => $userId,
            'ss' => $startStamp,
            'aa' => EQUESTIONS_BOL_FeedService::ACTIVITY_ASK
        ));
    }
}