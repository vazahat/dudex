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
class EQUESTIONS_BOL_FollowDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_BOL_FollowDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_BOL_FollowDao
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
        return 'EQUESTIONS_BOL_Follow';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'equestions_follow';
    }

    public function addFollow( $userId, $questionId )
    {
        $dto = $this->findFollow($userId, $questionId);

        if ( $dto === null )
        {
            $dto = new EQUESTIONS_BOL_Follow();
            $dto->userId = $userId;
            $dto->timeStamp = time();
            $dto->questionId = $questionId;
            $this->save($dto);
        }

        return $dto;
    }

    public function findFollow( $userId, $questionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('questionId', $questionId);

        return $this->findObjectByExample($example);
    }

    public function findByQuestionId( $questionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $questionId);

        return $this->findListByExample($example);
    }

    public function findFollowCount( $questionId, $userContext = array(), $ignoreUsers = array() )
    {
        $notIn = '1';

        if ( !empty($ignoreUsers) )
        {
            $notIn = 'userId NOT IN ("' . implode('","', $ignoreUsers) . '")';
        }

        $query = 'SELECT COUNT(*) FROM ' . $this->getTableName() . ' WHERE questionId=:q AND ' . $notIn;

        $out = $this->dbo->queryForColumn($query, array(
            'q' => $questionId
        ));

        return $out;
    }

    public function findFollowList( $questionId, $userContext = array(), $ignoreUsers = array() )
    {
        $notIn = '1';
        $order = 'timeStamp DESC';

        if ( !empty($ignoreUsers) )
        {
            $notIn = 'userId NOT IN ("' . implode('","', $ignoreUsers) . '")';
        }

        if ( !empty($userContext) )
        {
            $order = 'IF( userId IN ("' . implode('","', $userContext) . '"), 1, 0) DESC';
        }

        $query = 'SELECT * FROM ' . $this->getTableName() . ' WHERE questionId=:q AND ' . $notIn . ' ORDER BY ' . $order;

        $out = $this->dbo->queryForObjectList($query, $this->getDtoClassName(), array(
            'q' => $questionId
        ));

        return $out;
    }

    public function removeFollow( $userId, $questionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);
        $example->andFieldEqual('questionId', $questionId);

        return $this->deleteByExample($example);
    }
}