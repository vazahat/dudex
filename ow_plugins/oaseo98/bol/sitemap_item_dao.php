<?php

/**
 * Copyright (c) 2011 Sardar Madumarov
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * @author Sardar Madumarov <madumarov@gmail.com>
 * @package oaseo.bol
 */
class OASEO_BOL_SitemapItemDao extends OW_BaseDao
{
    const TYPE = 'type';
    const VALUE = 'value';
    const DATA = 'data';
    const ADD_TS = 'addTs';

    const VALUE_BROKEN_LINK = 1;
    const VALUE_IMAGE = 2;
    const VALUE_EXT_LINK = 3;
    const VALUE_BROKEN_EXT_LINK = 4;
    const VALUE_BROKEN_IMAGE = 5;

    /**
     * Singleton instance.
     *
     * @var OASEO_BOL_SitemapItem
     */
    private static $classInstance;

    /**
     * Returns an instance of class (singleton pattern implementation).
     *
     * @return OASEO_BOL_SitemapItem
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
     * Constructor.
     */
    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * @see OW_BaseDao::getDtoClassName()
     *
     */
    public function getDtoClassName()
    {
        return 'OASEO_BOL_SitemapItem';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'oaseo_sitemap_item';
    }

    public function clearTable()
    {
        $this->dbo->query("TRUNCATE TABLE `" . $this->getTableName() . "`");
    }

    /**
     * @param string $value
     * @param string $type
     * @return OASEO_BOL_SitemapItemDao
     */
    public function findItem( $value, $type )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::VALUE, $value);
        $example->andFieldEqual(self::TYPE, $type);

        return $this->findObjectByExample($example);
    }
    
    /**
     * @param string $value
     * @return OASEO_BOL_SitemapItemDao
     */
    public function findItemByValue( $value )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::VALUE, $value);

        return $this->findObjectByExample($example);
    }

    /**
     * @param string $type
     * @return array
     */
//    public function findItemsByType( $type )
//    {
//        $example = new OW_Example();
//        $example->andFieldEqual(self::TYPE, $type);
//
//        return $this->findListByExample($example);
//    }

    /**
     * @param string $type
     * @return int
     */
    public function findItemsCountByType( $type )
    {
        $example = new OW_Example();
        $example->andFieldEqual(self::TYPE, $type);

        return $this->countByExample($example);
    }

    /**
     * @return array
     */
//    public function getAllImages()
//    {
//        $query = "SELECT `i`.*, `p`.`url` FROM `".$this->getTableName()."` AS `i`
//            LEFT JOIN `".OASEO_BOL_SitemapPageItemDao::getInstance()->getTableName()."` AS `pi` ON (`i`.`id` = `pi`.`itemId`)
//            LEFT JOIN `".OASEO_BOL_SitemapPageDao::getInstance()->getTableName()."` AS `p` ON (`pi`.`pageId` = `p`.`id`)
//            WHERE `i`.`type` = ".self::VALUE_IMAGE." GROUP BY `i`.`id`";
//
//        return $this->dbo->queryForList($query);
//    }

    public function findItemsByType( $type, $first, $count )
    {
        $result = $this->dbo->queryForColumnList("SELECT `id` FROM `".$this->getTableName()."` WHERE `type` = ".$type." ORDER BY `id` LIMIT ?,?", array($first, $count));
        
        if( !$result )
        {
            return array();
        }
        
        $query = "SELECT `i`.*, `p`.`url` FROM `".$this->getTableName()."` AS `i`
            LEFT JOIN `".OASEO_BOL_SitemapPageItemDao::getInstance()->getTableName()."` AS `pi` ON (`i`.`id` = `pi`.`itemId`)
            LEFT JOIN `".OASEO_BOL_SitemapPageDao::getInstance()->getTableName()."` AS `p` ON (`pi`.`pageId` = `p`.`id`)
            WHERE `pi`.`type` = ". ( $type == OASEO_BOL_SitemapItemDao::VALUE_BROKEN_LINK ? OASEO_BOL_SitemapPageItemDao::TYPE_VALUE_PAGE :  OASEO_BOL_SitemapPageItemDao::TYPE_VALUE_ITEM)." AND `i`.`id` IN ( ".$this->dbo->mergeInClause($result)." ) ";
        
        return $this->dbo->queryForList($query);
    }
}
