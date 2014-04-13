<?php

/**
 * Copyright (c) 2013, Oxwall CandyStore
 * All rights reserved.

 * ATTENTION: This commercial software is intended for use with Oxwall Free Community Software http://www.oxwall.org/
 * and is licensed under Oxwall Store Commercial License.
 * Full text of this license can be found at http://www.oxwall.org/store/oscl
 */

/**
 * Data Access Object for `ocsaffiliates_affiliate` table.
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_affiliates.bol
 * @since 1.5.3
 */
class OCSAFFILIATES_BOL_AffiliateDao extends OW_BaseDao
{
    /**
     * Singleton instance.
     *
     * @var OCSAFFILIATES_BOL_AffiliateDao
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
     * @return OCSAFFILIATES_BOL_AffiliateDao
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
        return 'OCSAFFILIATES_BOL_Affiliate';
    }

    /**
     * @see OW_BaseDao::getTableName()
     *
     */
    public function getTableName()
    {
        return OW_DB_PREFIX . 'ocsaffiliates_affiliate';
    }

    /**
     * @param $email
     * @return OCSAFFILIATES_BOL_Affiliate
     */
    public function findByEmail( $email )
    {
        $example = new OW_Example();
        $example->andFieldEqual('email', $email);
        
        return $this->findObjectByExample($example);
    }

    public function findByUserId( $userId )
    {
        $example = new OW_Example();
        $example->andFieldEqual('userId', $userId);

        return $this->findObjectByExample($example);
    }

    /**
     * @param $offset
     * @param $limit
     * @param $sortBy
     * @param $sortOrder
     * @return array
     */
    public function getList( $offset, $limit, $sortBy, $sortOrder )
    {
        $clickDao = OCSAFFILIATES_BOL_ClickDao::getInstance();
        $signupDao = OCSAFFILIATES_BOL_SignupDao::getInstance();
        $saleDao = OCSAFFILIATES_BOL_SaleDao::getInstance();
        $payoutDao = OCSAFFILIATES_BOL_PayoutDao::getInstance();

        if ( in_array($sortBy, array('name', 'registerStamp', 'status')) )
        {
            $sortBy =  '`a`.`'.$sortBy.'`';
        }
        else
        {
            $sortBy = '`'.$sortBy.'`';
        }
        
    	$sql = "SELECT `a`.*, `clickCount`, `clickAmount`, `signupCount`, `signupAmount`, `saleCount`, `saleAmount`,
    	    `clickAmount` +  `signupAmount` + `saleAmount` AS `earnings`, `payouts`,
    	    `clickAmount` +  `signupAmount` + `saleAmount` - `payouts` AS `balance`
    	    FROM
    	    (
    	        SELECT `a`.*,
    	        ( SELECT COUNT(`cc`.`id`) FROM `".$clickDao->getTableName()."` AS `cc` WHERE `cc`.`affiliateId` = `a`.`id` GROUP BY `cc`.`affiliateId` ) AS `clickCount`,
    	        ( SELECT COALESCE(SUM(`ca`.`bonusAmount`), 0) FROM `".$clickDao->getTableName()."` AS `ca` WHERE `ca`.`affiliateId` = `a`.`id` GROUP BY `ca`.`affiliateId` ) AS `clickAmount`,
    	        ( SELECT COUNT(`suc`.`id`) FROM `".$signupDao->getTableName()."` AS `suc` WHERE `suc`.`affiliateId` = `a`.`id` GROUP BY `suc`.`affiliateId` ) AS `signupCount`,
    	        ( SELECT COALESCE(SUM(`sua`.`bonusAmount`), 0) FROM `".$signupDao->getTableName()."` AS `sua` WHERE `sua`.`affiliateId` = `a`.`id` GROUP BY `sua`.`affiliateId` ) AS `signupAmount`,
                ( SELECT COUNT(`sc`.`id`) FROM `".$saleDao->getTableName()."` AS `sc` WHERE `sc`.`affiliateId` = `a`.`id` GROUP BY `sc`.`affiliateId` ) AS `saleCount`,
    	        ( SELECT COALESCE(SUM(`sa`.`bonusAmount`), 0) FROM `".$saleDao->getTableName()."` AS `sa` WHERE `sa`.`affiliateId` = `a`.`id` GROUP BY `sa`.`affiliateId` ) AS `saleAmount`,
    	        ( SELECT COALESCE(SUM(`p`.`amount`), 0) FROM `".$payoutDao->getTableName()."` AS `p` WHERE `p`.`affiliateId` = `a`.`id` GROUP BY `p`.`affiliateId`) AS `payouts`
    	        FROM `".$this->getTableName()."` AS `a`
    	    ) AS `a`
    	    ORDER BY ".$sortBy." ".$sortOrder."
    	    LIMIT :offset, :limit";

    	return $this->dbo->queryForList($sql, array('offset' => $offset, 'limit' => $limit));
    }

    /**
     * Counts affiliates with 'unverified' status
     *
     * @return int
     */
    public function countUnverified()
    {
        $example = new OW_Example();
        $example->andFieldEqual('status', 'unverified');

        return $this->countByExample($example);
    }
}