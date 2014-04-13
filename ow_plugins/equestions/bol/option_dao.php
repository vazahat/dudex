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
class EQUESTIONS_BOL_OptionDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var EQUESTIONS_BOL_OptionDao
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return EQUESTIONS_BOL_OptionDao
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
        return 'EQUESTIONS_BOL_Option';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'equestions_option';
    }

    public function findListWithAnswerCountList( $id, $startStamp, $priorUsers = array(), $limit = null )
    {
        $answerDao = EQUESTIONS_BOL_AnswerDao::getInstance();
        $limitSql = empty($limit) ? '' : 'LIMIT ' . $limit[0] . ', ' . $limit[1];

        if ( empty($priorUsers) )
        {
            $query ='SELECT o.*, count(DISTINCT a.id) AS answerCount FROM ' . $this->getTableName() . ' o ' .
                'LEFT JOIN ' . $answerDao->getTableName() . ' a ON o.id = a.optionId ' .
                'WHERE o.questionId=:q AND o.timeStamp <= :ss
                    GROUP BY o.id
                    ORDER BY answerCount DESC, o.timeStamp, o.id ' . $limitSql;
        }
        else
        {
            $query ='SELECT o.*, count(DISTINCT a.id) AS answerCount FROM ' . $this->getTableName() . ' o ' .
                'LEFT JOIN ' . $answerDao->getTableName() . ' a ON o.id = a.optionId ' .
                'LEFT JOIN ' . $answerDao->getTableName() . ' a2 ON o.id = a2.optionId AND a2.userId IN (' . implode(', ', $priorUsers) . ') ' .
                'WHERE o.questionId=:q AND o.timeStamp <= :ss
                    GROUP BY o.id
                    ORDER BY count(DISTINCT a2.userId) DESC, answerCount DESC, o.timeStamp, o.id ' . $limitSql;
        }

        $list = $this->dbo->queryForList($query, array(
            'q' => $id,
            'ss' => $startStamp
        ));

        $countList = array();
        $optionList = array();

        foreach ( $list as $row )
        {
            $countList[$row['id']] = $row['answerCount'];
            unset($row['answerCount']);

            $option = new EQUESTIONS_BOL_Option;
            foreach ( $row as $k => $v )
            {
                $option->$k = $v;
            }

            $optionList[$row['id']] = $option;
        }

        return array(
            'countList' => $countList,
            'optionList' => $optionList
        );
    }

    public function findCountByQuestionId( $id )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $id);

        return $this->countByExample($example);
    }

    public function findByText( $questionId, $text )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $questionId);
        $example->andFieldEqual('text', $text);

        return $this->findObjectByExample($example);
    }

    public function findByQuestionId( $questionId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('questionId', $questionId);

        return $this->findListByExample($example);
    }
}