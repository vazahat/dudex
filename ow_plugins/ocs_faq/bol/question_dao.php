<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsfaq_question` table.
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.bol
 * @since 1.0
 */
class OCSFAQ_BOL_QuestionDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSFAQ_BOL_QuestionDao
     */
    private static $classInstance;

    /**
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Returns an instance of class.
     *
     * @return OCSFAQ_BOL_QuestionDao
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
        return 'OCSFAQ_BOL_Question';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsfaq_question';
    }
    
    public function getMaxOrder()
    {
    	$sql = "SELECT MAX(`order`) FROM `" . $this->getTableName() . "`";

    	return $this->dbo->queryForColumn($sql);
    }
    
    public function findAllQuestions()
    {
    	$example = new OW_Example();
    	$example->setOrder('`order` ASC');

    	return $this->findListByExample($example);
    }
    
    public function findFeaturedQuestions()
    {
        $example = new OW_Example();
        $example->andFieldEqual('isFeatured', 1);
        $example->setOrder('`order` ASC');

        return $this->findListByExample($example);
    }
    
    public function findListByCategoryId( $id )
    {
    	$example = new OW_Example();
    	$example->andFieldEqual('categoryId', $id);
    	$example->setOrder('`order` ASC');
    	
    	return $this->findListByExample($example);
    }
    
    public function countQuestionsAssignedToCategories()
    {
    	$sql = "SELECT COUNT(*) FROM `".$this->getTableName()."` WHERE `categoryId` > 0 ";
    	
    	return $this->dbo->queryForColumn($sql);
    }
    
    public function findUnassignedQuestions()
    {
    	$sql = "SELECT * FROM `".$this->getTableName()."` WHERE `categoryId` = 0 OR `categoryId` IS NULL ORDER BY `order` ASC";
    	
    	return $this->dbo->queryForObjectList($sql, $this->getDtoClassName());
    }
}