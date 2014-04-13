<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsaffiliates_sale` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_SaleDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_BOL_SaleDao
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
     * @return OCSAFFILIATES_BOL_SaleDao
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
        return 'OCSAFFILIATES_BOL_Sale';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsaffiliates_sale';
    }

    /**
     * @param int $affiliateId
     */
    public function deleteByAffiliateId( $affiliateId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('affiliateId', $affiliateId);
        
        $this->deleteByExample($example);
    }

    /**
     * @param int $affiliateId
     * @return int
     */
    public function countByAffiliateId( $affiliateId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('affiliateId', $affiliateId);
        
        return $this->countByExample($example);
    }

    /**
     * @param int $affiliateId
     * @return float
     */
    public function getSumByAffiliateId( $affiliateId )
    {
        $sql = "SELECT SUM(`bonusAmount`) FROM `".$this->getTableName()."`
        	WHERE `affiliateId` = :id";
        
        return $this->dbo->queryForColumn($sql, array('id' => $affiliateId));
    }

    /**
     * Returns list of sales that were not tracked by the affiliate system
     *
     * @param $limit
     * @return array
     */
    public function getUntrackedSales( $limit )
    {
        $saleDao = BOL_BillingSaleDao::getInstance();
        $affiliateUserDao = OCSAFFILIATES_BOL_AffiliateUserDao::getInstance();

        $sql = "SELECT `bs`.* FROM `".$saleDao->getTableName()."` AS `bs`
            INNER JOIN `".$affiliateUserDao->getTableName()."` AS `au` ON (`bs`.`userId` = `au`.`userId`)
            LEFT JOIN `".$this->getTableName()."` AS `as` ON(`bs`.`id`=`as`.`saleId`)
            WHERE `bs`.`status` = 'delivered' AND `as`.`id` IS NULL
            ORDER BY `bs`.`timeStamp` ASC
            LIMIT :limit";

        return $this->dbo->queryForObjectList(
            $sql,
            BOL_BillingSaleDao::getInstance()->getDtoClassName(),
            array('limit' => $limit)
        );
    }
}