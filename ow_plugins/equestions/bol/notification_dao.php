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
class EQUESTIONS_BOL_NotificationDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_BOL_NotificationDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_BOL_NotificationDao
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
        return 'EQUESTIONS_BOL_Notification';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'equestions_notification';
    }

    public function findNotification( $questionId, $type, $userId )
    {
        $example = new OW_Example();

        $example->andFieldEqual('type', $type);
        $example->andFieldEqual('questionId', $questionId);
        $example->andFieldEqual('userId', $userId);

        return $this->findObjectByExample($example);
    }

    public function deleteNotification( $questionId, $type, $userId )
    {
        $example = new OW_Example();

        $example->andFieldEqual('type', $type);
        $example->andFieldEqual('questionId', $questionId);
        $example->andFieldEqual('userId', $userId);

        return $this->deleteByExample($example);
    }

    public function markListViewed( $idList, $viewed = 1 )
    {
        if ( empty($idlist) )
        {
            return;
        }

        $query = 'UPDATE ' . $this->getTableName() . ' SET viewed=:viewed WHERE id IN (' . implode(',', $idlist) . ')';

        $this->dbo->query($query, array(
            'viewed' => $viewed
        ));
    }

    public function findList( $userId, $limit, $viewed = null )
    {
        $viewedSql = $viewed === null ? '1' : 'viewed=' . (int) $viewed;

        $query = 'SELECT * FROM ' . $this->getTableName() . ' WHERE userId=:u AND ' . $viewedSql . ' ORDER BY special, timeStamp DESC LIMIT :ls, :lo';

        return $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'u' => $userId,
            'ls' => $limit[0],
            'lo' => $limit[1]
        ));
    }

    public function findCount( $userId, $viewed = null )
    {
        $viewedSql = $viewed === null ? '1' : 'viewed=' . (int) $viewed;

        $query = 'SELECT COUNT(id) FROM ' . $this->getTableName() . ' WHERE userId=:u AND ' . $viewedSql;

        return $this->dbo->queryForColumn($query, array(
            'u' => $userId
        ));
    }

    public function findSentList( $questionId, $senderId, $type = null )
    {
        $example = new OW_Example();

        if ( !empty($type) )
        {
            $example->andFieldEqual('type', $type);
        }

        $example->andFieldEqual('questionId', $questionId);
        $example->andFieldEqual('senderId', $senderId);

        return $this->findListByExample($example);
    }


    public function findListByQuestionId( $questionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $questionId);

        return $this->findListByExample($example);
    }
}