<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsfaq_category` table.
 * 
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_faq.bol
 * @since 1.0
 */
class OCSFAQ_BOL_CategoryDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSFAQ_BOL_CategoryDao
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
     * @return OCSFAQ_BOL_CategoryDao
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
        return 'OCSFAQ_BOL_Category';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsfaq_category';
    }
    
    public function getList()
    {
    	$example = new OW_Example();
    	$example->setOrder('`order` ASC');

    	return $this->findListByExample($example);
    }
    
    public function getMaxOrder()
    {
    	$sql = "SELECT MAX(`order`) FROM `" . $this->getTableName() . "`";

    	return $this->dbo->queryForColumn($sql);
    }
}