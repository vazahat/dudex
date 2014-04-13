<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsaffiliates_click` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_ClickDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_BOL_ClickDao
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
     * @return OCSAFFILIATES_BOL_ClickDao
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
        return 'OCSAFFILIATES_BOL_Click';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsaffiliates_click';
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
     * Returns affiliate earning statistics for a certain period
     *
     * @param $affiliateId
     * @param $start
     * @param $end
     * @return array
     */
    public function getAffiliateEarningStat( $affiliateId, $start, $end )
    {
        $signupDao = OCSAFFILIATES_BOL_SignupDao::getInstance();
        $saleDao = OCSAFFILIATES_BOL_SaleDao::getInstance();

        $sql =
        "SELECT result.* FROM
        (
          SELECT `id`, `bonusAmount`, `clickDate` AS `timestamp` FROM `".$this->getTableName()."`
          WHERE `affiliateId` = :id AND `clickDate` BETWEEN :start AND :end

        UNION

          SELECT `id`, `bonusAmount`, `signupDate` AS `timestamp` FROM `".$signupDao->getTableName()."`
          WHERE `affiliateId` = :id AND `signupDate` BETWEEN :start AND :end

        UNION

          SELECT `id`, `bonusAmount`, `saleDate` AS `timestamp` FROM `".$saleDao->getTableName()."`
          WHERE `affiliateId` = :id AND `saleDate` BETWEEN :start AND :end
        ) AS `result`
        ORDER BY `result`.`timestamp` ASC
        ";

        return $this->dbo->queryForList($sql, array('id' => $affiliateId, 'start' => $start, 'end' => $end));
    }

    /**
     * Returns all events log for affiliate
     *
     * @param $affiliateId
     * @param $offset
     * @param $limit
     * @return array
     */
    public function getEventsLog( $affiliateId, $offset, $limit )
    {
        $signupDao = OCSAFFILIATES_BOL_SignupDao::getInstance();
        $saleDao = OCSAFFILIATES_BOL_SaleDao::getInstance();

        $sql =
        "SELECT result.* FROM
        (
          SELECT `id`, `bonusAmount`, `clickDate` AS `timestamp`, 'click' AS `type` FROM `".$this->getTableName()."`
          WHERE `affiliateId` = :id

        UNION

          SELECT `id`, `bonusAmount`, `signupDate` AS `timestamp`, 'signup' AS `type` FROM `".$signupDao->getTableName()."`
          WHERE `affiliateId` = :id

        UNION

          SELECT `id`, `bonusAmount`, `saleDate` AS `timestamp`, 'sale' AS `type` FROM `".$saleDao->getTableName()."`
          WHERE `affiliateId` = :id
        ) AS `result`
        ORDER BY `result`.`timestamp` DESC LIMIT :offset, :limit";

        return $this->dbo->queryForList($sql, array('id' => $affiliateId, 'offset' => $offset, 'limit' => $limit));
    }
}