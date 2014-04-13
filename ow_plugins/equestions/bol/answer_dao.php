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
class EQUESTIONS_BOL_AnswerDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_BOL_AnswerDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_BOL_AnswerDao
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
        return 'EQUESTIONS_BOL_Answer';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'equestions_answer';
    }

    /**
     *
     * @return EQUESTIONS_BOL_Answer
     */
    public function findAnswer( $userId, $optionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('optionId', $optionId);
        $example->andFieldEqual('userId', $userId);

        return $this->findObjectByExample($example);
    }

    public function findByQuestionIdAndUserId( $questionId, $userId )
    {
        $optionDao = EQUESTIONS_BOL_OptionDao::getInstance();

        $query ='SELECT a.* FROM ' . $this->getTableName() . ' a ' .
                'INNER JOIN ' . $optionDao->getTableName() . ' o ON a.optionId=o.id ' .
                'WHERE o.questionId=:q AND a.userId=:u';

        return $this->dbo->queryForColumn($query, array(
            'q' => $questionId,
            'u' => $userId
        ));
    }

    public function findListWithUserIdList( $optionId, $userIds, $limit = null )
    {
        if (empty($userIds))
        {
            return array();
        }

        $example = new OW_Example();
        $example->andFieldEqual('optionId', $optionId);
        $example->andFieldInArray('userId', $userIds);

        if ( !empty($limit) )
        {
            $example->setLimitClause(0, $limit);
        }

        $example->setOrder('timeStamp DESC');

        return $this->findListByExample($example);
    }

    public function findList( $optionId, $limit = null )
    {
        $example = new OW_Example();
        $example->andFieldEqual('optionId', $optionId);

        if ( !empty($limit) )
        {
            $example->setLimitClause(0, $limit);
        }

        $example->setOrder('timeStamp DESC');

        return $this->findListByExample($example);
    }

    public function findByOptionId( $optionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('optionId', $optionId);

        return $this->findListByExample($example);
    }

    public function findTotalCountByQuestionId( $questionId )
    {
        $optionDao = EQUESTIONS_BOL_OptionDao::getInstance();

        $query ='SELECT COUNT(a.id) FROM ' . $this->getTableName() . ' a ' .
                'INNER JOIN ' . $optionDao->getTableName() . ' o ON a.optionId=o.id ' .
                'WHERE o.questionId=:q';

        return $this->dbo->queryForColumn($query, array(
            'q' => $questionId
        ));
    }

    public function findMaxCountByQuestionId( $questionId )
    {
        $optionDao = EQUESTIONS_BOL_OptionDao::getInstance();

        $query ='SELECT count(a.id) FROM ' . $this->getTableName() . ' a ' .
                'INNER JOIN ' . $optionDao->getTableName() . ' o ON a.optionId=o.id ' .
                'WHERE o.questionId=:q GROUP BY o.id ORDER BY count(a.id) DESC limit 1';

        return (int) $this->dbo->queryForColumn($query, array(
            'q' => $questionId
        ));
    }

    public function findCountList( $optionIds )
    {
        if ( empty($optionIds) )
        {
            return array();
        }

        $query ='SELECT optionId, count(id) count FROM ' . $this->getTableName() .
                ' WHERE optionId IN (' . implode(', ', $optionIds) . ') GROUP BY optionId';

        $list = $this->dbo->queryForList($query);
        $out = array();
        foreach ( $list as $row )
        {
            $out[$row['optionId']] = $row['count'];
        }

        foreach ($optionIds as $oid)
        {
            $out[$oid] = empty($out[$oid]) ? 0 : $out[$oid];
        }

        return $out;
    }

    public function findCount( $optionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('optionId', $optionId);

        return $this->countByExample($example);
    }

    public function findUserAnswerList( $userId, $optionIds )
    {
        $example = new OW_Example();
        $example->andFieldInArray('optionId', $optionIds);
        $example->andFieldEqual('userId', $userId);

        return $this->findListByExample($example);
    }
}